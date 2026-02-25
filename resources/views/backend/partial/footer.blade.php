{{-- Footer --}}
            <footer class="footer">
                <div class="container">
                    <div class="row align-items-center flex-row-reverse">
                        <div class="col-md-12 col-sm-12 text-center">
                            {{ $systemSettings?->copyright_text 
                                ?? "Copyright © ".date('Y')." ".($systemSettings?->site_name ?? 'Noa').". Designed with ❤️ by ".($systemSettings?->designer_name ?? 'Spruko').". All rights reserved" }}
                        </div>
                    </div>
                </div>
            </footer>