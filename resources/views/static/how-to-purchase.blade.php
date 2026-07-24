@extends('static.layouts.site')

@section('title', 'How to Purchase any Publication — Dav/Devs')

@section('content')
<article style="text-align:center;max-width:1280px;margin:0 auto;padding:48px 36px;">
    <h1 style="font-family:'Syne',sans-serif;font-size:clamp(24px,4vw,32px);font-weight:800;color:var(--text-primary);margin:0 0 8px;">How to Purchase <span style="color:var(--accent);">any</span> Publication</h1>

    <section style="margin-top:48px;">
        <div>
            <img src="{{ asset("how-to-purchase/0001.png") }}" alt="Homepage" style="display:block;max-width:50%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
        </div>

        <p style="margin-top:12px;">Navigate to the publication listing page (e.g. eBooks) of the <span style="color:var(--accent);">desired</span> publication.</p>
    </section>

    <section style="margin-top:48px;">
        <div>
            <img src="{{ asset("how-to-purchase/0002.png") }}" alt="Publication listing page" style="display:block;max-width:50%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
        </div>

        <p style="margin-top:12px;">Select the publication you want to <span style="color:var(--accent);">purchase</span>.</p>
    </section>

    <section style="margin-top:48px;">
        <div style="display:flex;gap:12px;max-width:100%">
            <img src="{{ asset("how-to-purchase/0009.png") }}" alt="Publication detail (scroll down) page" style="flex:1 1 0;min-width:0;width:50%;object-fit:contain;border:1px solid var(--border-default);border-radius:5px;">
            <img src="{{ asset("how-to-purchase/0004.png") }}" alt="Publication detail page" style="flex:1 1 0;min-width:0;width:50%;object-fit:contain;border:1px solid var(--border-default);border-radius:5px;">
        </div>

        <p style="margin-top:12px;">On the publication detail page, scroll down to the purchase section and click the <span style="color:var(--accent);">Buy Now</span> button. You will be brought to a <span style="color:var(--accent);">Lemon Squeezy</span> checkout page.</p>

        <p style="font-style: italic; opacity: 0.7;">Note: Phrasing may vary depending on the publication.</p>
    </section>

    <section style="margin-top:48px;">
        <div>
            <img src="{{ asset("how-to-purchase/0005.png") }}" alt="Lemon Squeezy checkout page" style="display:block;max-width:50%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
        </div>

        <p style="margin-top:12px;">Fill in all your payment details and complete the purchase.</p>
    </section>

    <section style="margin-top:48px;">
        <div>
            <img src="{{ asset("how-to-purchase/0006.png") }}" alt="Lemon Squeezy confirmation page" style="display:block;max-width:50%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
        </div>

        <p style="margin-top:12px;">You will see a confirmation of your purchase.</p>
    </section>

    <section style="margin-top:48px;">
        <div>
            <img src="{{ asset("how-to-purchase/0007.png") }}" alt="Email notification" style="display:block;max-width:50%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
        </div>

        <p style="margin-top:12px;">Wait for 5-10 minutes and check your email. Click on the <span style="color:var(--accent);">View Order</span> button.</p>

        <p style="font-style: italic; opacity: 0.7;">Note: The order confirmation link may expire after a certain period. So make sure to check your email promptly.</p>
    </section>

    <section style="margin-top:48px;">
        <div>
            <img src="{{ asset("how-to-purchase/0008.png") }}" alt="Order page" style="display:block;max-width:50%;margin-inline:auto;border:1px solid var(--border-default);border-radius:5px;">
        </div>

        <p style="margin-top:12px;">On this page, you will see the details of your order and download the eBook as a PDF.</p>
    </section>

    <p style="margin-top:48px;">
        Thank you for your support! If you have any questions, feel free to reach out via the <a href="#" style="color:var(--accent);text-decoration:none;">contact form</a>.
    </p>
</article>
@endsection
