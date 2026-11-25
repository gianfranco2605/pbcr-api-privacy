# PBCR API Privacy

Removes private owner data from the property REST API of pb-cr.com (`/wp-json/wp/v2/propiedad`) and, optionally, limits the property data block to users with an API key.

## What it does

The RealHomes theme adds a `property_meta` field to every property in the REST API. It also contained private data.

**Step 1 – always on.** These keys are removed from `property_meta` for everyone:

- `inspiry_property_owner_name`
- `inspiry_property_owner_contact`
- `inspiry_property_owner_address`
- `REAL_HOMES_property_private_note`
- `inspiry_message_to_reviewer`
- `rvr_property_owner`

Price, code, location, gallery, agents and the other public data keep working.

**Step 2 – optional.** Under *Settings → Reading → Property API*, the checkbox makes `property_meta` visible only to users with the `pbcr_read_property_api` capability. Administrators and the *API reader (read only)* role have it. Turn it on only after the integrations (e.g. CIMA) authenticate with their key.

The WordPress editor (`context=edit`, for users who can edit the property) is never modified. The `pbcr-agent-mode` plugin is not affected because it reads meta directly with `get_post_meta()`.

## API key for an integration

1. *Users → Add New*, role **API reader (read only)**.
2. In that user's profile, *Application Passwords* → name it (e.g. "CIMA") → copy the password.
3. Send it through a private channel, never by email.

Requests use HTTP Basic auth: `curl -u "user:application password" https://pb-cr.com/wp-json/wp/v2/propiedad`.

## Installation

Upload the `pbcr-api-privacy` folder to `wp-content/plugins/` (without `vendor/`) and activate it from *Plugins*.

## Removal

Deactivate it from *Plugins*, or delete the folder via FTP. Uninstalling from the panel also removes the role and the option.

## Developer filters

```php
// Add more private keys.
add_filter( 'pbcr_api_privacy_private_fields', function ( $fields ) {
	$fields[] = 'my_private_key';
	return $fields;
} );

// Change the capability needed in step 2.
add_filter( 'pbcr_api_privacy_reader_capability', function () {
	return 'edit_posts';
} );
```

## Development

```bash
composer install
composer run lint
```

## File structure

```
pbcr-api-privacy/
├── pbcr-api-privacy.php        # Main file, autoloader, activation
├── uninstall.php               # Removes option and role
├── includes/
│   ├── Plugin.php              # Registers components
│   ├── Roles.php               # API reader role and capability
│   ├── Rest/PropertyPrivacy.php # REST response filter
│   └── Admin/Settings.php      # Checkbox in Settings → Reading
└── languages/                  # es_ES translation
```
