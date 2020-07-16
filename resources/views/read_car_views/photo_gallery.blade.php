<?php
    use App\AuctionCarImage;
/**
 * @var TYPE_NAME $dataTypeContent
 * @var TYPE_NAME $image
 * @var TYPE_NAME $index
 */
$gallery = $dataTypeContent->images;
?>
@if(isset($dataTypeContent))
    <div class="gallery">
        @if(!empty($gallery))
            @foreach($gallery as $index => $image)
                @if(AuctionCarImage::isXMLTypeOfFile($image->path))
                    <a class="gallery__link" href="<?= asset('placeholder.png') ?>" data-fancybox="gallery"
                       data-caption="No photos available">
                        <img class="gallery__image" src="<?= asset('placeholder.png') ?>" alt=""/>
                    </a>
                @else
                    <a class="gallery__link" href="<?= asset($image->uri) ?>" data-fancybox="gallery"
                       data-caption="Photo № <?= $index + 1 ?> of <?= count($gallery) ?>">
                        <img class="gallery__image" src="<?= asset($image->uri) ?>" alt=""/>
                    </a>
                @endif
            @endforeach
        @else
            <a class="gallery__link" href="<?= asset('placeholder.png') ?>" data-fancybox="gallery"
               data-caption="No photos available">
                <img class="gallery__image" src="<?= asset('placeholder.png') ?>" alt=""/>
            </a>
        @endif
    </div>
@else
    <span>Not available</span>
@endif
