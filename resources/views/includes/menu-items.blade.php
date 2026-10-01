<div class="gallery-items min-pad  lightgallery three-column fl-wrap " style="margin-bottom:50px;">
@foreach ($menuItems as $key => $menuItem)
    <div class="gallery-item">
        <div class="grid-item-holder hov_zoom">
            <a href="{{ $menuItem->image ? \Asset::get_image_path('menu-item-image', 'normal', $menuItem->image ) : asset('assets/img/isologo.png') }}" class="box-media-zoom   popup-image"><i class="fal fa-search"></i></a>
            <img src="{{ $menuItem->image ? \Asset::get_image_path('menu-item-image', 'normal', $menuItem->image ) : asset('assets/img/isologo.png') }}" alt="" class="">
            <div class="caption__image font-courier_new"><p><b>{{ $menuItem->title }}</b></p></div>
        </div>
    </div>
@endforeach
</div>
{{-- <div class="col-md-4" style="margin-bottom: 20px !important;">
    <div class="team-box">
        <div class="team-photo">
            <img src="{{ $menuItem->image ? \Asset::get_image_path('menu-item-image', 'normal', $menuItem->image ) : asset('assets/img/isologo.png') }}" alt="" class="respimg-two">
            <div class="overlay"></div>
            <div class="team-social">
                <ul class="no-list-style">
                    <li><a href="{{ url('menu/'. $category->code) }}"><i class="fa fa-search"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="team-info fl-wrap">
            <h4>{{ $menuItem->title }}</h4>
        </div>
    </div>
</div> --}}
