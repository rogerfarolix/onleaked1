@extends('layouts.public')

@section('title', 'Contact - ' . config('app.name', 'Onleaked'))

@section('content')
    <div class="pt-32 pb-20 px-6">
        <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-12">
            <!-- Info -->
            <div class="fade-up">
                <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
                    Get in <span class="bg-gradient-to-r from-violet-400 to-rose-400 bg-clip-text text-transparent">Touch</span>
                </h1>
                <p class="text-zinc-400 text-lg mb-10">Have questions, feedback, or partnership inquiries? We'd love to hear from you.</p>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="font-medium text-white">Email</p>
                            <a href="mailto:contact@nealix.org" class="text-zinc-400 hover:text-violet-400 transition-colors">contact@nealix.org</a>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-medium text-white">Location</p>
                            <p class="text-zinc-400">Nealix HQ — Remote-first</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <p class="font-medium text-white">Response Time</p>
                            <p class="text-zinc-400">Usually within 24 hours</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="fade-up" style="animation-delay:.2s" x-data="contactForm()">
                <div class="glass-card rounded-2xl p-8 relative overflow-hidden">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-violet-500/10 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <template x-if="sent">
                            <div class="text-center py-10">
                                <div class="w-16 h-16 mx-auto rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h3 class="text-xl font-semibold text-white mb-2">Message Sent!</h3>
                                <p class="text-zinc-400">Thank you for reaching out. We'll get back to you soon.</p>
                            </div>
                        </template>
                        <template x-if="!sent">
                            <form @submit.prevent="send" class="space-y-5">
                                <div>
                                    <label for="contact-name" class="block text-sm font-medium text-zinc-300 mb-1.5">Name</label>
                                    <input id="contact-name" x-model="name" type="text" required class="block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-violet-500/50 transition-colors" placeholder="Your name">
                                </div>
                                <div>
                                    <label for="contact-email" class="block text-sm font-medium text-zinc-300 mb-1.5">Email</label>
                                    <input id="contact-email" x-model="email" type="email" required class="block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-violet-500/50 transition-colors" placeholder="you@example.com">
                                </div>
                                <div>
                                    <label for="contact-subject" class="block text-sm font-medium text-zinc-300 mb-1.5">Subject</label>
                                    <select id="contact-subject" x-model="subject" class="block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:border-violet-500/50 focus:ring-violet-500/50 transition-colors">
                                        <option value="general" class="bg-zinc-900">General Inquiry</option>
                                        <option value="support" class="bg-zinc-900">Technical Support</option>
                                        <option value="partnership" class="bg-zinc-900">Partnership</option>
                                        <option value="bug" class="bg-zinc-900">Bug Report</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="contact-message" class="block text-sm font-medium text-zinc-300 mb-1.5">Message</label>
                                    <textarea id="contact-message" x-model="message" rows="4" required class="block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-violet-500/50 transition-colors resize-none" placeholder="How can we help?"></textarea>
                                </div>
                                <button type="submit" class="w-full py-3 px-4 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-all duration-200">Send Message</button>
                            </form>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function contactForm() {
        return {
            name: '', email: '', subject: 'general', message: '', sent: false,
            send() {
                // For now, just show success. Backend mail logic can be added later.
                this.sent = true;
            }
        }
    }
</script>
@endpush
