<?php

namespace ABBYY\CloudAPI\API\Normalizer;

use Joli\Jane\Normalizer\ReferenceNormalizer;
use Joli\Jane\Normalizer\NormalizerArray;
class NormalizerFactory
{
    public static function create()
    {
        $normalizers = array();
        $normalizers[] = new ReferenceNormalizer();
        $normalizers[] = new NormalizerArray();
        $normalizers[] = new TranslateParamsNormalizer();
        $normalizers[] = new TranslateResponseNormalizer();
        $normalizers[] = new ErrorModelNormalizer();
        $normalizers[] = new BadRequestBodyModelNormalizer();
        $normalizers[] = new FileInfoViewModelNormalizer();
        $normalizers[] = new OcrSettingsViewModelNormalizer();
        $normalizers[] = new TextStatisticsViewModelNormalizer();
        $normalizers[] = new OcrStatisticsViewModelNormalizer();
        $normalizers[] = new OcrWarningViewModelNormalizer();
        $normalizers[] = new ObjectNormalizer();
        $normalizers[] = new FormatDescriptorViewModelNormalizer();
        $normalizers[] = new SubmitOrderModelNormalizer();
        $normalizers[] = new GetFileModelNormalizer();
        $normalizers[] = new FullOrderViewModelNormalizer();
        $normalizers[] = new TranslationViewModelNormalizer();
        $normalizers[] = new OrderStatisticsViewModelNormalizer();
        $normalizers[] = new FileLinkViewModelNormalizer();
        $normalizers[] = new GetProposalModelNormalizer();
        $normalizers[] = new ProposalViewModelNormalizer();
        $normalizers[] = new QuoteViewModelNormalizer();
        $normalizers[] = new OrdersPageViewModelNormalizer();
        $normalizers[] = new OrderViewModelNormalizer();
        $normalizers[] = new GetOrdersPageModelNormalizer();
        $normalizers[] = new ServicePriceViewModelNormalizer();
        $normalizers[] = new UnitPriceViewModelNormalizer();
        $normalizers[] = new DiscountViewModelNormalizer();
        return $normalizers;
    }
}