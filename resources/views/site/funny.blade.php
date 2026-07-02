<x-site-layout title="Funny">
    <section style="max-width:520px;margin:0 auto;padding:80px 36px;text-align:center;"
             x-data="{
                quip: null, revealed: false, timeLeft: 30, timer: null,
                load() {
                    this.revealed = false; clearInterval(this.timer);
                    fetch('{{ route('api.quips.random') }}' + (this.quip ? '?exclude=' + this.quip.id : ''))
                        .then(r => r.json()).then(d => {
                            this.quip = d; this.timeLeft = 30;
                            if (d.variant === 'qa') {
                                this.timer = setInterval(() => {
                                    this.timeLeft--;
                                    if (this.timeLeft <= 0) { this.revealed = true; clearInterval(this.timer); }
                                }, 1000);
                            } else { this.revealed = true; }
                        });
                }
             }" x-init="load()">
        <div style="background:var(--joke-card-bg);border-radius:10px;padding:32px;min-height:200px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:16px;">
            <template x-if="quip">
                <div>
                    <template x-if="quip.variant === 'qa'">
                        <div>
                            <p style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);" x-text="quip.question"></p>
                            <template x-if="!revealed">
                                <div style="margin-top:16px;">
                                    <div style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--joke-timer-num);" x-text="timeLeft + 's'"></div>
                                    <button @click="revealed = true; clearInterval(timer)" style="margin-top:10px;font-family:'JetBrains Mono',monospace;font-size:8px;color:var(--text-muted);border:1px solid var(--border-default);background:none;padding:4px 10px;border-radius:4px;cursor:pointer;">Show answer</button>
                                </div>
                            </template>
                            <template x-if="revealed">
                                <div x-transition style="margin-top:16px;background:var(--joke-answer-bg);padding:16px;border-radius:8px;">
                                    <p style="font-family:'Inter',sans-serif;font-size:14px;color:var(--text-body);" x-text="quip.punchline"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="quip.variant === 'statement'">
                        <p style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);" x-text="quip.punchline"></p>
                    </template>
                </div>
            </template>
        </div>
        <button @click="load()" style="margin-top:20px;font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--accent);background:none;border:1px solid var(--accent-border);padding:6px 16px;border-radius:5px;cursor:pointer;">↻ Next joke</button>
        <p style="font-family:'Inter',sans-serif;font-size:10px;color:var(--text-faint);margin-top:32px;">Jokes are for fun only and not meant to offend. If one lands wrong, click next.</p>
    </section>
</x-site-layout>
