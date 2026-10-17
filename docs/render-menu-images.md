# Menu images on Render Hobby

New menu uploads are stored in the existing application database in the
`menu_item_images` table. Neon retains these rows across Render restarts and
redeployments; no Render disk, shell access, new service, or extra credentials
are required. A photo is served from a versioned public `/menu-images/...` URL.
Image content is kept out of normal menu-list queries and cached by browsers.

Deploy the updated Docker image. Its startup command applies pending migrations,
creates the legacy public storage link, and starts Apache. No manual migration or
`storage:link` command is needed. The startup wrapper also runs before an existing
Render Docker command override, so the service can keep its current start command.

After deployment, sign in as an owner or manager, open Menu catalog, edit an item,
and upload a JPG, PNG, or GIF up to 2 MB. The edit form previews its saved photo.
Guest accounts still cannot upload files.

Existing legacy image paths remain supported while their files exist. Files that
were lost from Render's temporary filesystem cannot be reconstructed from their
filenames in Neon; upload those photos again to move them into database storage.

This is intended for a small POS menu, not a large media library. Base64 storage
uses roughly one third more database space than the original file, and counts
against the existing Neon storage/transfer allowance. Consider compressed images
or object storage if the catalog becomes large.

Checks: `MenuImageTest` covers uploads, cache validation, public display without a
local file, replacement/versioning, edits, deletion, and file-size validation.
