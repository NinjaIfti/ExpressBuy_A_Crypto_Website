@if (isset($about['single']))
    <section class="about-section">
        <div class="container">
            <div class="row g-4 g-sm-5">
                <div class="col-lg-6">
                    <div class="about-thum">
                         <img src="{{@$about['mediaFile']}}" alt="...">
                    
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-content mx-auto">
                        <h2 class="section-title">
                            @lang(wordSplice(@$about['single']['title'])['exceptTwo']) <span class="highlight">@lang(wordSplice(@$about['single']['title'])['lastTwo'])</span>
                        </h2>
                        <p class="cmn-para-text">@lang(@$about['single']['description'])</p>
                    </div>
                    <div class="btn-area">
                        <a href="{{ @$about['single']['media']->my_link }}" class="cmn-btn">@lang(@$about['single']['button_name'])</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-shape1"></div>
        <div class="shape shape2 opacity-100">
            <img src="{{ asset($themeTrue . 'img/coin/5.png') }}" alt="...">
        </div>
    </section>
@endif
<!-- About section end -->
