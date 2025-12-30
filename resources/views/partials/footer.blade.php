<footer id="contact" class="border-t border-border bg-card">
    <div class="container py-12 lg:py-16">
        <div class="grid gap-10 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <a href="{{ route('home') }}" class="mb-4 flex items-center gap-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary">
                        <svg class="h-5 w-5 text-primary-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-foreground">DRMS</span>
                </a>
                <p class="mb-6 max-w-md text-sm leading-relaxed text-muted-foreground">
                    Device Registration & IMEI Management System. A secure, centralized
                    platform for device verification, registration, and ownership management.
                </p>
                <div class="flex flex-col gap-3">
                    <a href="mailto:support@drms.gov" class="flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9 6 9-6" />
                            <rect x="3" y="5" width="18" height="14" rx="2" />
                        </svg>
                        support@drms.gov
                    </a>
                    <a href="tel:+1800123456" class="flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 16.5v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3.5 5.18 2 2 0 0 1 5.5 3h3a2 2 0 0 1 2 1.72l.5 3a2 2 0 0 1-.57 1.73l-1.2 1.2a16 16 0 0 0 6.6 6.6l1.2-1.2a2 2 0 0 1 1.73-.57l3 .5a2 2 0 0 1 1.72 2z" />
                        </svg>
                        1-800-DRMS-HELP
                    </a>
                </div>
            </div>

            <div>
                <h4 class="mb-4 font-bold text-foreground">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('home') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Home</a></li>
                    <li><a href="{{ route('verification') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">IMEI Verification</a></li>
                    <li><a href="{{ route('transfer') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Ownership Transfer</a></li>
                    <li><a href="{{ route('register-device') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Register Device</a></li>
                    <li><a href="#contact" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 font-bold text-foreground">Legal</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Privacy Policy</a></li>
                    <li><a href="#" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Terms of Service</a></li>
                    <li><a href="#" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Cookie Policy</a></li>
                    <li><a href="#" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Accessibility</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 border-t border-border pt-8">
            <p class="text-center text-sm text-muted-foreground">
                &copy; {{ date('Y') }} Device Registration & IMEI Management System. All rights reserved.
            </p>
        </div>
    </div>
</footer>
