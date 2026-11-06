# Legacy app findings

Read from `application/app` (Laravel 5.2, v1.5) for piece `pce_e1f795559c824fac`.
Everything below is from the code. Items marked **CHECK** need confirming against real rows or the live plugin.

## 1. Timestamps and "never expires"

- Models store timestamps as Unix **seconds**. Setters write `$value->timestamp`
  (`RecordBaseModel::setCreatedAttribute`, `setUpdatedAttribute`, `setExpiresAttribute`;
  `PastRecordBaseModel` for `pastCreated`, `expired`). Dashboard queries compare
  `lastSeen` with `strtotime(...)` and group with `FROM_UNIXTIME(lastSeen, ...)`.
- **Never expires = `0`.** Controllers write `Carbon::createFromTimeStamp(0)` when the
  expiry field is empty. The `active` scope is `expires >= now OR expires = 0`;
  `outdated` is `expires < now AND expires != 0`. `getDurationAttribute` returns null
  when `expires->timestamp == 0`.
- Both are written in the same units as the plugin reads them only if the live plugin
  also uses seconds. **CHECK** against a few real rows (a permanent ban, a recent
  `created`).
- Quirk: the `active` scope does not match `expires IS NULL` (see section 7).

## 2. Actor written by the app

- The app never records the logged-in web user in the plugin DB. Every write uses the
  player row **named `Console`**:
  `Player::on($server)->where('name', 'Console')->firstOrFail()->id`
  (bans, mutes, warnings, notes, and the `actor_id` of every record row).
- So the console actor is found by **name**, not by a fixed UUID in the code.
  **CHECK** the UUID of that `bm_players` row (the plugin normally creates it).
- Consequence: moderator accountability must live in the app's own database in the
  rewrite (an audit log), since the plugin tables cannot carry a web user.

## 3. Write flows (all in `application/app/Http/Controllers`)

All create/edit forms identify the player **by name** (`where name = ...`), not UUID.
Validation: `reason` and `player` required; `expires` must be in the future if given.

**Ban create** (`PlayerBanController@store`)
- Checks the player exists, then checks `player_bans` for an existing row with that
  `player_id` (message "already banned"). Inserts `player_id, actor_id (Console), reason,
  expires (0 if empty), created = now, updated = now`. `silent` and other columns are
  left to their defaults.

**Ban edit** (`@update`)
- Updates `player_id` (the form can even change the player), `reason`, `expires`,
  `updated = now`. `actor_id` and `created` are not touched. No duplicate check on edit.

**Ban remove** (`@destroy`)
- Inserts into `player_ban_records`: `player_id`, `reason`, `expired = ban.expires`,
  `actor_id = Console`, `pastActor_id = ban.actor_id`, `pastCreated = ban.created`,
  `created = now`; then deletes the ban. **`createdReason` is not set**, though the column
  is `NOT NULL` with no default. It works only if the server is not in strict mode.
  **CHECK** (the rewrite should write `''` explicitly).
- Not wrapped in a transaction.

**Mute** (`PlayerMuteController`) mirrors bans exactly: same duplicate check on create,
same record move on remove (`player_mute_records`). `soft`, `silent`, `onlineOnly`,
`pausedRemaining` (and `remainingOnlineTime` on records) are never written, so they take
column defaults.

**Warning** (`PlayerWarningController`)
- Create: `player_id, actor_id (Console), reason, created = now`. **No duplicate check,
  no `expires`, no `points`, no `read`**: `expires` stays NULL, `points` defaults to 1.00.
- Edit: `reason` and player. Remove: plain delete, no record table.

**Note** (`PlayerNoteController`)
- Create: `player_id, actor_id (Console), message, created = now`. Edit and delete exist.

**Kick**: the web UI can only **delete** a kick (`PlayerKickController@destroy`); kicks
are created in-game by the plugin. Same for **ban/mute records**: delete only
(`PlayerBanRecordController`, `PlayerMuteRecordController`).

## 4. Plugin tables the app touches

Read/write: `players`, `player_bans`, `player_mutes`, `player_kicks` (read, delete),
`player_warnings`, `player_notes`, `player_ban_records`, `player_mute_records`.
Read only (statistics): `ip_bans`, `ip_ban_records` (counts only; `IpBan` and
`IpBanRecord` models exist, no controller writes them).
**Never touched**: name bans, IP mutes, IP range bans, player history, reports and
report tables, rollbacks, all `*_all` tables.
(Table names are used unprefixed; the old app set a per-server prefix, which the rewrite
fixes in config.)

## 5. Features and routes (`routes.php`)

Public (no login):
- `/` search by player name; `/players/{uuid}` public profile (active bans/mutes plus a
  merged history of bans, mutes, kicks, warnings, past bans, past mutes; notes excluded);
  `/statistics` (counts); `/api/search-playername*` (name autocomplete, JSON);
  `/api/version`.

Staff (`/admin`, login required):
- Moderator (role >= 3): dashboard (active ban/mute counts, 30-day active-player
  chart, totals), active bans / mutes / warnings lists, activity stream (last 25 of each
  type), player list/search/profile (includes notes), cache flush.
- Admin (role >= 6): create/edit/remove bans, mutes, warnings, notes; delete kicks and
  records; user management (`UserController`, which also stops editing users above your own
  role).
- Superuser (role >= 8): server management (`ServerController`), which the rewrite drops.
- `/admin/config` is a stub: its handler returns "Sorry, this is still WIP. Settings were
  NOT saved." before doing anything.
- Install wizard (`/install`) and server management are multi-server concerns to drop.

## 6. Local users and roles

- Local DB is **SQLite** (`storage/database.sqlite`, connection `local`), separate from
  the plugin DBs. Tables: `users`, `password_resets`, `servers`.
- `users`: `id, name, email (unique), password (string 60, bcrypt), role (int, default 0),
  remember_token, timestamps`.
- Roles: **0 normal (inactive), 3 moderator, 6 admin, 8 superuser**. Role 0 means "needs
  activation": the dashboard counts them as new accounts. Anyone can self-register
  (`AuthController` create) and then waits for a higher-role user to promote them.
- Users can only edit users whose role is not higher than their own.
- Passwords use `bcrypt()`.

## 7. Quirks not to copy

- Multi-server plumbing: global `Config::set('database.default', ...)` swapping in
  relations, `Route::bind('player')` looping over servers, per-server forms.
- Player identified by **name** on create/edit; names can change. Use UUID.
- Ban edit lets you reassign a ban to a different player.
- Record `createdReason` omitted (strict-mode risk); no transactions around the
  insert-then-delete on removal.
- Warnings created from the web have `expires = NULL`, which the `active` scope
  (`expires >= now OR expires = 0`) treats as **not active**. They never show in the active
  warnings list. Decide the intended behaviour.
- Web actions always attribute to `Console`; nobody can tell which moderator acted.
- Dashboard recomputes counts with raw per-request queries behind a tiny cache (1 to 10
  minutes); pointless at this scale.
- `PlayerController@index` ends in `dd($players)` (debug dump).
- Self-registration with manual role promotion; the rewrite imports users and forces a
  new password instead (see project decisions).

## Still to check against real data

1. Timestamp unit and `0` for permanent (section 1).
2. UUID and presence of the `Console` row in `bm_players` (section 2).
3. Whether `createdReason` rejects missing values on the live DB (section 3).
4. Whether the plugin expects `silent`, `soft` or `onlineOnly` set when it reads rows
   written by the web app (defaults appear fine in legacy, but the live server has run
   with them).
