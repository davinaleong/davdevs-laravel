{{-- How to Purchase self-service help dialog.
     Requires a parent element with x-data containing { helpOpen: false }.
--}}
<div x-show="helpOpen" x-cloak
    @keydown.escape.window="helpOpen = false"
    style="position:fixed;inset:0;z-index:200;display:flex;align-items:flex-start;justify-content:center;padding:20px;overflow-y:auto;">

    {{-- Backdrop --}}
    <div @click="helpOpen = false"
        style="position:fixed;inset:0;background:rgba(0,0,0,.75);backdrop-filter:blur(4px);"></div>

    {{-- Dialog panel --}}
    <div role="dialog" aria-modal="true" aria-labelledby="htp-title"
        style="position:relative;z-index:1;background:var(--bg-default);border:0.5px solid var(--border-default);border-radius:12px;width:100%;max-width:600px;margin:auto;box-shadow:0 24px 64px rgba(0,0,0,.5);">

        {{-- Sticky header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:0.5px solid var(--border-default);position:sticky;top:0;background:var(--bg-default);border-radius:12px 12px 0 0;z-index:1;">
            <h2 id="htp-title"
                style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);margin:0;">
                How to Purchase <span style="color:var(--accent);">any</span> Publication
            </h2>
            <button @click="helpOpen = false"
                style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:22px;line-height:1;padding:0 4px;"
                aria-label="Close">&times;</button>
        </div>

        {{-- Scrollable body --}}
        <article style="padding:24px;text-align:center;display:flex;flex-direction:column;gap:36px;">

            <section>
                <img src="{{ asset('how-to-purchase/0001.png') }}" alt="Homepage"
                    style="display:block;max-width:60%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
                <p style="margin-top:12px;color:var(--text-secondary);font-size:14px;">
                    Navigate to the publication listing page (e.g. eBooks) of the
                    <span style="color:var(--accent);">desired</span> publication.
                </p>
            </section>

            <section>
                <img src="{{ asset('how-to-purchase/0002.png') }}" alt="Publication listing page"
                    style="display:block;max-width:60%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
                <p style="margin-top:12px;color:var(--text-secondary);font-size:14px;">
                    Select the publication you want to <span style="color:var(--accent);">purchase</span>.
                </p>
            </section>

            <section>
                <div style="display:flex;gap:10px;">
                    <img src="{{ asset('how-to-purchase/0009.png') }}" alt="Publication detail — scroll down"
                        style="flex:1 1 0;min-width:0;object-fit:contain;border:1px solid var(--border-default);border-radius:5px;">
                    <img src="{{ asset('how-to-purchase/0004.png') }}" alt="Publication detail page"
                        style="flex:1 1 0;min-width:0;object-fit:contain;border:1px solid var(--border-default);border-radius:5px;">
                </div>
                <p style="margin-top:12px;color:var(--text-secondary);font-size:14px;">
                    On the publication detail page, scroll down to the purchase section and click the
                    <span style="color:var(--accent);">Buy Now</span> button. You will be brought to a
                    <span style="color:var(--accent);">Lemon Squeezy</span> checkout page.
                </p>
                <p style="font-style:italic;opacity:.6;font-size:12px;margin-top:6px;">
                    Note: Phrasing may vary depending on the publication.
                </p>
            </section>

            <section>
                <img src="{{ asset('how-to-purchase/0005.png') }}" alt="Lemon Squeezy checkout page"
                    style="display:block;max-width:60%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
                <p style="margin-top:12px;color:var(--text-secondary);font-size:14px;">
                    Fill in all your payment details and complete the purchase.
                </p>
            </section>

            <section>
                <img src="{{ asset('how-to-purchase/0006.png') }}" alt="Lemon Squeezy confirmation page"
                    style="display:block;max-width:60%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
                <p style="margin-top:12px;color:var(--text-secondary);font-size:14px;">
                    You will see a confirmation of your purchase.
                </p>
            </section>

            <section>
                <img src="{{ asset('how-to-purchase/0007.png') }}" alt="Email notification"
                    style="display:block;max-width:60%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
                <p style="margin-top:12px;color:var(--text-secondary);font-size:14px;">
                    Wait 5–10 minutes and check your email. Click the
                    <span style="color:var(--accent);">View Order</span> button.
                </p>
                <p style="font-style:italic;opacity:.6;font-size:12px;margin-top:6px;">
                    Note: The order confirmation link may expire — check your email promptly.
                </p>
            </section>

            <section>
                <img src="{{ asset('how-to-purchase/0008.png') }}" alt="Order page"
                    style="display:block;max-width:60%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
                <p style="margin-top:12px;color:var(--text-secondary);font-size:14px;">
                    On this page, you will see the order details and can download the eBook as a PDF.
                </p>
            </section>

        </article>
    </div>
</div>
