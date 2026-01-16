=== Liaison Site Prober Viewer ===
Contributors: liason
Tags: Gutenberg, block, site-prober, logs
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Description: Gutenberg Block for viewing logs (from liaison-site-prober plugin) inside posts/pages. Dynamic block fetches logs via REST API.

=== Architecture ===

Plugin structure:

liaison-site-prober-viewer/
├── src/
│   ├── edit.js          # Block editor UI
│   └── editor.scss      # Block editor styles
├── build/
│   └── index.js         # Compiled block code
├── __tests__/           # Jest unit tests
├── liaison-site-prober-viewer.php  # Plugin bootstrap
└── package.json / node_modules   # JS dependencies

Architecture Diagram (textual):

[Database: liaison-site-prober activity table]
           |
           v
[WordPress REST API endpoint: wp-json/site-prober/v1/logs]
           |
           v
[Gutenberg dynamic block: liaison-site-prober-viewer]
           |
           v
[Editor/UI: renders logs in a table inside the block]

=== Data Flow ===

1. liaison-site-prober writes activity logs into the database.
2. The REST API (`wp-json/site-prober/v1/logs`) exposes logs in JSON format.
3. The dynamic Gutenberg block (`Edit` component) calls `apiFetch({ path: '/site-prober/v1/logs' })`.
4. The block maintains internal state:
   - `loading` → shows Spinner
   - `error` → shows Notice
   - `logs` → renders table rows
5. Logs are displayed dynamically in the block editor and frontend (if rendered).

=== Why Dynamic Block ===

- Logs are constantly changing; storing static markup would show stale data.
- Dynamic block fetches fresh logs each render.
- No need for manual updates in post content.
- Leverages REST API + React state for live data.

=== Security Considerations ===

- Permissions:
  - REST endpoint uses `permission_callback` to restrict access.
- Sanitization:
  - All output in the block uses `esc_html()` to prevent XSS.
- Deactivation checks:
  - Plugin checks that `liaison-site-prober` is installed and meets minimum version.
- Avoid exposing sensitive data:
  - Only logs intended for admin or authorized users are returned.
- Use `wp_die()` for activation errors, preventing unsafe state.

=== Trade-offs / Limitations ===

- Pros:
  - Easy to use in Gutenberg editor.
  - Always shows live logs.
  - Minimal custom PHP; relies on WordPress REST API.

- Cons:
  - Slightly slower in editor due to API fetch.
  - Unit testing does not hit real database (mocked).
  - Requires liaison-site-prober to be installed.
  - Not fully decoupled from REST API; block depends on API stability.

=== Installation ===

1. Install `liaison-site-prober` plugin (v1.2.0+ required).
2. Upload `liaison-site-prober-viewer` to `wp-content/plugins/`.
3. Activate plugin in WordPress admin.
4. Add "Liaison Site Prober Viewer" block to a post/page.
5. Logs will automatically load in the block editor.

=== Testing ===

- Run JS unit tests:
  ```bash
  npm ci
  npm test

Block uses Jest with @wordpress/scripts preset.

REST API calls are mocked; tests cover loading, empty, and error states.

=== Changelog ===

= 1.0.0 =

Initial release: dynamic Gutenberg block for viewing liaison-site-prober logs.

