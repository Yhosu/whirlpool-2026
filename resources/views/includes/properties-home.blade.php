<div class="container">
    <div class="row align-items-center">
        <div class="col-md-6 col-12 mb-2">
            <h1 class="mb-0">{{ $count }} Properties</h1>
        </div>
        <div class="col-md-6 col-12 justify-content-md-end justify-content-center jus d-flex mb-2">
            <ul class="pagination mt-0 mb-0">
                @for( $i = 1; $i <= $pages; $i++ )
                <li class="page-item {{ $i == $actualPage ? 'active' : '' }}">
                    <a  class="page-link"
                        href="javascript:void(0);"
                        onclick="filterByPage( {{ $i }}, '{{json_encode($filters)}}', {{ $i == $actualPage ? true : false }} )">{{ $i }}</a>
                </li>
                @endfor
            </ul>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="row mt-4">
                @foreach ( ( $properties ) as $property ) 
                <div class="col-sm-4">
                    <div class="property-item">
                        <a href="{{ url( 'property/' . $property['id'] ) }}">
                            <div class="property-image bg-overlay-gradient-04">
                                <img class="img-fluid" src="{{ $mapWithKeys[$property['id']]['thumb_medium'] ?? url('assets/img/logo.jpg') }}" alt="">
                                <div class="property-agent-popup"> </div>
                            </div>
                        </a>
                        <div class="property-details">
                            <div class="property-details-inner">
                                <h5 class="property-title"><a href="{{ url( 'property/' . $property['id'] ) }}">{{ $property['external_name'] ?? $property['name'] ?? '' }} </a></h5>
                                <ul class="property-info list-unstyled d-flex">
                                    <li class="flex-fill property-m-sqft"><i class="fas fa-user"></i>Guests<span>{{ $mapWithKeys[$property['id']]['max_guests'] ?? '-' }}</span></li>
                                    <li class="flex-fill property-bed"><i class="fas fa-bed"></i>Bed<span>{{ $mapWithKeys[$property['id']]['bedrooms'] ?? '-' }}</span></li>
                                    <li class="flex-fill property-bath"><i class="fas fa-bath"></i>Bath<span>{{ $mapWithKeys[$property['id']]['bathrooms'] ?? '-' }}</span></li>
                                </ul>
                            </div>
                            <div class="property-btn">
                                <a class="property-link" href="{{ url('property/' . $property['id'] ) }}">See Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-12 col-12 justify-content-md-end justify-content-center jus d-flex mb-2">
            <ul class="pagination mt-0 mb-0">
                @for( $i = 1; $i <= $pages; $i++ )
                <li class="page-item {{ $i == $actualPage ? 'active' : '' }}">
                    <a  class="page-link"
                        href="javascript:void(0);"
                        onclick="filterByPage( {{ $i }}, '{{json_encode($filters)}}', {{ $i == $actualPage ? true : false }} )">{{ $i }}</a>
                </li>
                @endfor
            </ul>
        </div>
    </div>
</div>