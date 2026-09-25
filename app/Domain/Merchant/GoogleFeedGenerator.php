<?php

namespace App\Domain\Merchant;

use App\Domain\Merchant\Support\PriceFormatter;
use App\DTO\Merchant\GoogleProductData;
use App\Models\Product;
use XMLWriter;

class GoogleFeedGenerator
{
    public function __construct(private readonly GoogleProductMapper $mapper)
    {
    }

    public function toXml(): string
    {
        $w = new XMLWriter();
        $w->openMemory();
        $w->setIndent(true);
        $w->startDocument('1.0', 'UTF-8');
        $w->startElement('rss');
        $w->writeAttribute('version', '2.0');
        $w->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
        $w->startElement('channel');
        $w->writeElement('title', (string) config('feed.title'));
        $w->writeElement('link', url('/'));
        $w->writeElement('description', (string) config('feed.description'));

        Product::with(['images', 'categories'])
            ->orderBy('id')
            ->chunk(200, function ($products) use ($w) {
                foreach ($products as $product) {
                    $dto = $this->mapper->map($product);
                    if ($dto) {
                        $this->writeItem($w, $dto);
                    }
                }
            });

        $w->endElement();
        $w->endElement();
        $w->endDocument();

        return $w->outputMemory();
    }

    private function writeItem(XMLWriter $w, GoogleProductData $dto): void
    {
        $w->startElement('item');
        $w->writeElement('g:id', $dto->id);
        $w->writeElement('title', $dto->title);
        $w->startElement('description');
        $w->writeCdata($dto->description);
        $w->endElement();
        $w->writeElement('link', $dto->link);
        $w->writeElement('g:image_link', $dto->imageLink);

        foreach ($dto->additionalImageLinks as $url) {
            $w->writeElement('g:additional_image_link', $url);
        }

        $w->writeElement('g:availability', $dto->availability);
        $w->writeElement('g:condition', $dto->condition);
        $w->writeElement('g:price', $dto->price);
        if ($dto->salePrice) {
            $w->writeElement('g:sale_price', $dto->salePrice);
        }
        $w->writeElement('g:brand', $dto->brand);

        if ($dto->gtin) {
            $w->writeElement('g:gtin', $dto->gtin);
        }
        if ($dto->mpn) {
            $w->writeElement('g:mpn', $dto->mpn);
        }
        if (! $dto->identifierExists) {
            $w->writeElement('g:identifier_exists', 'no');
        }
        if ($dto->itemGroupId) {
            $w->writeElement('g:item_group_id', $dto->itemGroupId);
        }
        if ($dto->googleProductCategory !== '') {
            $w->writeElement('g:google_product_category', $dto->googleProductCategory);
        }
        if ($dto->productType !== '') {
            $w->writeElement('g:product_type', $dto->productType);
        }
        if ($dto->shippingWeight) {
            $w->writeElement('g:shipping_weight', $dto->shippingWeight);
        }

        $w->writeElement('g:content_language', $dto->contentLanguage);
        $w->writeElement('g:target_country', $dto->targetCountry);

        $shipPrice = PriceFormatter::format((float) config('feed.shipping_price', 0), $dto->currency);

        foreach ((array) config('feed.shipping_countries', ['CH']) as $country) {
            $w->startElement('g:shipping');
            $w->writeElement('g:country', (string) $country);
            $w->writeElement('g:service', (string) config('feed.shipping_service'));
            $w->writeElement('g:price', $shipPrice);
            $w->writeElement('g:min_handling_time', (string) (int) config('feed.shipping_handling_time_min', 1));
            $w->writeElement('g:max_handling_time', (string) (int) config('feed.shipping_handling_time_max', 1));
            $w->writeElement('g:min_transit_time', (string) (int) config('feed.shipping_transit_time_min', 0));
            $w->writeElement('g:max_transit_time', (string) (int) config('feed.shipping_transit_time_max', 1));
            $w->endElement();
        }

        $w->endElement();
    }
}
