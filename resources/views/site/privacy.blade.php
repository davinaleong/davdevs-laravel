<x-site-layout title="Privacy Policy">
    <article style="max-width:680px;margin:0 auto;padding:48px 36px;">
        <h1 style="font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:var(--text-primary);margin:0 0 24px;">Privacy Policy</h1>
        <div style="font-family:'Inter',sans-serif;font-size:13px;line-height:1.7;color:var(--text-body);">
            <p>This site is built with privacy as a default, not an afterthought. Here's what we collect and why, in line with Singapore's Personal Data Protection Act (PDPA).</p>

            <h2 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);margin-top:24px;">What we collect</h2>
            <ul>
                <li><strong>Anonymous like/reaction token</strong> — a random UUID stored in an HttpOnly, SameSite=Strict cookie for one year. It cannot identify you personally; it only lets you toggle a like on/off.</li>
                <li><strong>Hashed IP addresses</strong> — your IP is combined with a daily-rotating salt and hashed with SHA-256 before storage. We never store your raw IP. This is used only for basic rate limiting and abuse prevention.</li>
                <li><strong>Standard server logs</strong> — request method, path, response status, and duration, kept for 90 days then automatically deleted.</li>
            </ul>

            <h2 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);margin-top:24px;">What we don't do</h2>
            <ul>
                <li>No third-party analytics or tracking scripts (no Google Analytics, no Facebook Pixel).</li>
                <li>No advertising cookies.</li>
                <li>No selling or sharing of any data with third parties.</li>
            </ul>

            <h2 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);margin-top:24px;">Essential cookies</h2>
            <p>We use essential cookies only: your session (if you're logged in), CSRF protection, and the anonymous like token described above. These are required for the site to function and are not used for tracking.</p>

            <h2 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);margin-top:24px;">Contact</h2>
            <p>Questions about this policy can be directed via the contact links in the footer.</p>
        </div>
    </article>
</x-site-layout>
