# ART Story Database Architecture

## Scope

The database has two areas:

1. The active ART Story artwork, sales, inquiry, artist portal, and administration modules.
2. Retained legacy course and mock-test tables from the earlier application. They are not registered in the ART Story admin panel and should not be used by new artwork features.

## Active Domain Model

```text
users
  |-- artists.user_id
  |-- artwork_price_versions.changed_by_id
  |-- activity_log.subject / causer
  `-- roles and permissions

artists 1 ---- * artworks
artists 1 ---- * artwork_sales

artworks 1 ---- * artwork_price_versions
artworks 1 ---- * artwork_sales
artworks 1 ---- * artwork_inquiries
artworks * ---- 1 artwork_styles
artworks * ---- 1 artwork_subject_styles
artworks * ---- 1 artwork_mediums
artworks * ---- 1 artwork_sizes

buyers 1 ---- * artwork_sales
artwork_payment_methods 1 ---- * artwork_sales
artwork_payment_methods 1 ---- * artwork_tax_rates
artwork_tax_rates 1 ---- * artwork_sales

artwork_sales 1 ---- * artwork_email_logs
artwork_inquiries 1 ---- * artwork_email_logs
```

## Core Tables

### `artists`

Artist identity, contact details, biography, profile image, approval state, and active state.

### `artworks`

The catalogue record. It owns the artwork code, title, primary image, gallery image paths, canvas count, classifications, BDT/USD prices, availability status, and artist submission state.

`gallery_images` is a JSON array because gallery images are media paths rather than independent business records. The primary image remains in `image_path`.

### `artwork_price_versions`

Immutable historical price/status snapshots. A new row is created before a price or status change is persisted. This preserves the previous BDT/USD values and the user who changed them.

### `buyers`

Reusable buyer/customer records used by artwork sales. Sale rows also keep buyer snapshots (`buyer_name`, `buyer_email`, and related fields) so old invoices do not change when a buyer profile is edited.

### `artwork_sales`

One invoice/sale transaction. It supports one or many artworks.

Important fields:

- `currency`: `BDT` or `USD`.
- `line_items`: JSON snapshot of every purchased artwork.
- `subtotal_amount`, `tax_amount`, and `total_amount`: stored financial results.
- `tax_rate_id` plus tax country/name/percentage fields: selected rule and immutable sale snapshot.
- invoice email counters and last-send status: operational email tracking.

The JSON `line_items` snapshot is intentional. It prevents later artwork title, artist, image, or price changes from altering a historical invoice.

### `artwork_tax_rates`

Versioned tax rules by country, currency, and optional payment method. Rates are never overwritten for a completed sale; the selected rule is copied to the sale snapshot.

### `artwork_payment_methods`

Admin-managed payment methods used by sales and tax-rule matching.

### `artwork_inquiries`

Public artwork inquiries with customer name, WhatsApp number, email, message, artwork snapshot fields, status, admin notes, and contact timestamps.

### `artwork_email_logs`

Delivery history for sale invoices and inquiry responses. It records recipient, template key, subject, payload, sent/failed timestamps, and error text.

### `email_templates`

Admin-managed HTML/text templates keyed by business event, such as `artwork_sale_invoice` and `artwork_inquiry_response`.

### Virtual gallery tables

- `virtual_galleries`: published 3D exhibitions with title, cover image, description, visibility, display order, and optional automatic inclusion of every available artwork.
- `virtual_gallery_rooms`: editable room dimensions, materials, optional 360-degree panorama, and starting camera position.
- `virtual_gallery_artworks`: room placements that connect a catalogue artwork to a wall, coordinates, display size, frame color, rotation, and visibility.

The public 3D scene is assembled from these records at request time. When automatic catalogue mode is enabled, available artworks are distributed across generated rooms according to `artworks_per_room`; new artwork uploads appear without a separate gallery placement. The gallery record also stores its automatic room colors, dimensions, starting camera position, partition walls, and floor-grid settings so the public space can be managed entirely from admin. Artwork images and details continue to come from the existing catalogue, so edits in the artwork module remain the source of truth.

### Settings and content tables

- `site_settings`: branding, contact, social, SEO, and WhatsApp configuration.
- `mail_settings`: SMTP/provider configuration.
- `landing_pages`: landing-page content and featured artwork/artist selections.
- `notifications`: Filament admin notifications.
- `activity_log`: audit trail for model changes.

## Lifecycle Rules

1. An artist is approved before their active artworks appear publicly.
2. An artwork receives a unique artist-based code such as `AGB-P-01`.
3. Artwork price/status changes create a historical price-version row.
4. A sale stores immutable artwork, buyer, tax, and amount snapshots.
5. Saving a sale marks every line-item artwork as sold.
6. Sending an invoice or inquiry response creates an email-log row and records success or failure.
7. Artwork inquiries notify active admin users and remain trackable by status.

## Retained Legacy Tables

The following migration families belong to the previous course/mock-test application and are retained only for migration-history safety:

- `courses`, `course_enrollments`, and related checkout/content tables.
- `exam_formats`.
- `mock_test_exam_dates`, `mock_test_pricings`, `mock_test_registrations`, `mock_test_results`, `mock_test_booking_tokens`, and purchase-status tables.
- `mock_test_email_logs` and legacy mock-test email-template migrations.
- `coupon_codes`.

They are not part of the active ART Story admin panel. Historical migrations should remain in source control unless a deliberate database retirement plan is approved.

## Recommendations

### Near term

- Keep sale and invoice snapshots immutable after confirmation.
- Add database-level checks or service validation for supported currencies (`BDT`, `USD`) and non-negative money/tax values.
- Use a transaction when creating a sale and marking all line-item artworks sold.
- Add a unique constraint or retry handling around generated artwork codes for concurrent uploads.
- Add indexes for public catalogue filtering: `(is_active, status)`, `(artist_id, is_active)`, and active price lookups.

### Later, if reporting grows

Move `artwork_sales.line_items` into an `artwork_sale_items` table with one row per artwork while keeping snapshot columns there. This improves reporting, refunds, partial fulfilment, and joins without sacrificing historical accuracy.

Move `artworks.gallery_images` into an `artwork_images` table if images need captions, ordering, image type, moderation status, or per-image metadata.

### Data retirement

Retire legacy course/mock-test tables only through a separate, approved archival process. First export any required data, then deploy a staged deprecation migration and verify foreign keys and backups before dropping tables.
