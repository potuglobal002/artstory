<x-artstory.layout :site-settings="$siteSettings" title="Upload Artwork" active="artist_portal">
    <main class="mx-auto max-w-4xl px-6 py-12">
        <style>
            .artist-head { align-items: end; border-bottom: 1px solid #e5e7eb; display: flex; gap: 24px; justify-content: space-between; margin-bottom: 34px; padding-bottom: 26px; }
            .artist-kicker { color: #88884d; font-size: 12px; font-weight: 900; letter-spacing: .22em; margin-bottom: 12px; text-transform: uppercase; }
            .artist-title { font-family: "Playfair Display", serif; font-size: clamp(36px, 4vw, 58px); font-weight: 600; line-height: 1; }
            .artist-btn { align-items: center; border: 1px solid #111827; display: inline-flex; font-size: 12px; font-weight: 900; justify-content: center; letter-spacing: .12em; min-height: 44px; padding: 0 18px; text-transform: uppercase; }
            .artist-panel { border: 1px solid #e5e7eb; background: #fff; padding: 28px; }
            .artist-form { display: grid; gap: 16px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .artist-field span { display: block; font-size: 11px; font-weight: 900; letter-spacing: .12em; margin-bottom: 8px; text-transform: uppercase; }
            .artist-field input,
            .artist-field select,
            .artist-field textarea { border: 1px solid #d1d5db; min-height: 46px; outline: 0; padding: 11px 12px; width: 100%; }
            .artist-field small { color: #6b7280; display: block; font-size: 12px; line-height: 1.5; margin-top: 7px; }
            .artist-field textarea { min-height: 130px; resize: vertical; }
            .artist-upload-box { border: 1px dashed #9ca3af; display: grid; gap: 16px; padding: 16px; }
            .artist-preview { align-items: center; background: #f8fafc; border: 1px solid #e5e7eb; display: none; gap: 14px; grid-template-columns: 96px minmax(0, 1fr); padding: 12px; }
            .artist-preview.is-visible { display: grid; }
            .artist-preview img { aspect-ratio: 1; background: #fff; object-fit: cover; width: 96px; }
            .artist-preview strong { display: block; font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .artist-preview em { color: #6b7280; display: block; font-size: 12px; font-style: normal; margin-top: 4px; }
            .artist-gallery-preview { display: none; gap: 10px; grid-template-columns: repeat(auto-fill, minmax(72px, 1fr)); }
            .artist-gallery-preview.is-visible { display: grid; }
            .artist-gallery-preview img { aspect-ratio: 1; background: #fff; object-fit: cover; width: 100%; }
            .artist-custom-field { display: none; margin-top: 10px; }
            .artist-custom-field.is-visible { display: block; }
            .artist-submit { background: #111; color: #fff; font-size: 12px; font-weight: 900; letter-spacing: .12em; min-height: 48px; text-transform: uppercase; }
            .artist-submit:disabled { cursor: wait; opacity: .62; }
            .artist-errors { border: 1px solid #fecaca; background: #fef2f2; color: #991b1b; font-size: 14px; line-height: 1.6; margin-bottom: 24px; padding: 14px 16px; }
            @media (max-width: 760px) { .artist-head, .artist-form { display: grid; grid-template-columns: 1fr; } }
        </style>

        <header class="artist-head">
            <div>
                <p class="artist-kicker">Artist Dashboard</p>
                <h1 class="artist-title">Upload Artwork</h1>
            </div>
            <a href="{{ route('artist.dashboard') }}" class="artist-btn">Back to dashboard</a>
        </header>

        @if ($errors->any())
            <div class="artist-errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="artist-panel">
            <form method="POST" action="{{ route('artist.artworks.store') }}" enctype="multipart/form-data" class="artist-form">
                @csrf
                <input type="hidden" name="submission_token" value="{{ $submissionToken }}">
                <label class="artist-field md:col-span-2">
                    <span>Artwork title</span>
                    <input name="title" value="{{ old('title') }}" placeholder="Untitled">
                </label>
                <label class="artist-field md:col-span-2">
                    <span>Artwork image</span>
                    <div class="artist-upload-box">
                        <input type="file" name="image" accept="image/*" required data-artwork-image>
                        <div class="artist-preview" data-artwork-preview>
                            <img src="" alt="Selected artwork preview" data-artwork-preview-image>
                            <div>
                                <strong data-artwork-preview-name>Selected artwork</strong>
                                <em data-artwork-preview-meta>Ready to upload</em>
                            </div>
                        </div>
                    </div>
                    <small>Preview your artwork before submitting. JPG, PNG, and WebP are supported.</small>
                </label>
                <label class="artist-field md:col-span-2">
                    <span>Artwork gallery</span>
                    <div class="artist-upload-box">
                        <input type="file" name="gallery_images[]" accept="image/*" multiple data-artwork-gallery>
                        <div class="artist-gallery-preview" data-artwork-gallery-preview></div>
                    </div>
                    <small>Add extra angles, close-ups, or separate canvas images for the same artwork.</small>
                </label>
                <label class="artist-field">
                    <span>Style</span>
                    <select name="artwork_style_id" data-custom-select="custom_style">
                        <option value="">Select style</option>
                        @foreach ($styles as $style)
                            <option value="{{ $style->id }}" @selected(old('artwork_style_id') == $style->id)>{{ $style->name }}</option>
                        @endforeach
                        <option value="__custom" @selected(old('custom_style'))>Add new style</option>
                    </select>
                    <input @class(['artist-custom-field', 'is-visible' => old('custom_style')]) name="custom_style" value="{{ old('custom_style') }}" placeholder="Write new style name" data-custom-field="custom_style">
                </label>
                <label class="artist-field">
                    <span>Subject style</span>
                    <select name="artwork_subject_style_id" data-custom-select="custom_subject_style">
                        <option value="">Select subject style</option>
                        @foreach ($subjectStyles as $subjectStyle)
                            <option value="{{ $subjectStyle->id }}" @selected(old('artwork_subject_style_id') == $subjectStyle->id)>{{ $subjectStyle->name }}</option>
                        @endforeach
                        <option value="__custom" @selected(old('custom_subject_style'))>Add new subject style</option>
                    </select>
                    <input @class(['artist-custom-field', 'is-visible' => old('custom_subject_style')]) name="custom_subject_style" value="{{ old('custom_subject_style') }}" placeholder="Write new subject style" data-custom-field="custom_subject_style">
                </label>
                <label class="artist-field">
                    <span>Medium</span>
                    <select name="artwork_medium_id" data-custom-select="custom_medium">
                        <option value="">Select medium</option>
                        @foreach ($mediums as $medium)
                            <option value="{{ $medium->id }}" @selected(old('artwork_medium_id') == $medium->id)>{{ $medium->name }}</option>
                        @endforeach
                        <option value="__custom" @selected(old('custom_medium'))>Add new medium</option>
                    </select>
                    <input @class(['artist-custom-field', 'is-visible' => old('custom_medium')]) name="custom_medium" value="{{ old('custom_medium') }}" placeholder="Write new medium" data-custom-field="custom_medium">
                </label>
                <label class="artist-field">
                    <span>Size</span>
                    <select name="artwork_size_id" data-custom-select="custom_size">
                        <option value="">Select size</option>
                        @foreach ($sizes as $size)
                            <option value="{{ $size->id }}" @selected(old('artwork_size_id') == $size->id)>{{ $size->label() }}</option>
                        @endforeach
                        <option value="__custom" @selected(old('custom_size'))>Add new size</option>
                    </select>
                    <input @class(['artist-custom-field', 'is-visible' => old('custom_size')]) name="custom_size" value="{{ old('custom_size') }}" placeholder="Example: 76 x 76 cm" data-custom-field="custom_size">
                </label>
                <label class="artist-field">
                    <span>Year</span>
                    <input type="number" name="year" value="{{ old('year') }}" min="1000" max="{{ now()->addYear()->year }}">
                </label>
                <label class="artist-field">
                    <span>Canvas count</span>
                    <input type="number" name="canvas_count" value="{{ old('canvas_count', 1) }}" min="1" max="20" required>
                    <small>Use 2 or more when one artwork is made from multiple canvases.</small>
                </label>
                <label class="artist-field">
                    <span>Expected BDT price</span>
                    <input type="number" name="price" value="{{ old('price') }}" min="0" step="1">
                </label>
                <label class="artist-field">
                    <span>Expected USD price</span>
                    <input type="number" name="usd_price" value="{{ old('usd_price') }}" min="0" step="0.01">
                </label>
                <label class="artist-field md:col-span-2">
                    <span>Note</span>
                    <textarea name="note">{{ old('note') }}</textarea>
                </label>
                <button class="artist-submit md:col-span-2" data-submit-once>Submit for review</button>
            </form>
        </section>
    </main>

    <script>
        (() => {
            const imageInput = document.querySelector('[data-artwork-image]');
            const preview = document.querySelector('[data-artwork-preview]');
            const previewImage = document.querySelector('[data-artwork-preview-image]');
            const previewName = document.querySelector('[data-artwork-preview-name]');
            const previewMeta = document.querySelector('[data-artwork-preview-meta]');
            const galleryInput = document.querySelector('[data-artwork-gallery]');
            const galleryPreview = document.querySelector('[data-artwork-gallery-preview]');

            imageInput?.addEventListener('change', () => {
                const file = imageInput.files?.[0];

                if (! file) {
                    preview?.classList.remove('is-visible');
                    return;
                }

                previewImage.src = URL.createObjectURL(file);
                previewName.textContent = file.name;
                previewMeta.textContent = `${(file.size / 1024 / 1024).toFixed(2)} MB`;
                preview?.classList.add('is-visible');
            });

            galleryInput?.addEventListener('change', () => {
                const files = Array.from(galleryInput.files || []);
                galleryPreview.innerHTML = '';

                if (! files.length) {
                    galleryPreview?.classList.remove('is-visible');
                    return;
                }

                files.slice(0, 12).forEach((file) => {
                    const image = document.createElement('img');
                    image.src = URL.createObjectURL(file);
                    image.alt = file.name;
                    galleryPreview.appendChild(image);
                });

                galleryPreview?.classList.add('is-visible');
            });

            document.querySelectorAll('[data-custom-select]').forEach((select) => {
                const field = document.querySelector(`[data-custom-field="${select.dataset.customSelect}"]`);

                const syncField = () => {
                    const isCustom = select.value === '__custom' || field?.value;
                    field?.classList.toggle('is-visible', Boolean(isCustom));

                    if (! isCustom && field) {
                        field.value = '';
                    }
                };

                select.addEventListener('change', syncField);
                syncField();
            });

            document.querySelector('form.artist-form')?.addEventListener('submit', (event) => {
                const button = event.currentTarget.querySelector('[data-submit-once]');

                if (! button || button.disabled) {
                    event.preventDefault();
                    return;
                }

                button.disabled = true;
                button.textContent = 'Submitting...';
            });
        })();
    </script>
</x-artstory.layout>
