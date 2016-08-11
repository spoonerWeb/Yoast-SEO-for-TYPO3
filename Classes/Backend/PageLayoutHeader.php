<?php
namespace YoastSeoForTypo3\YoastSeo\Backend;


use TYPO3\CMS;

class PageLayoutHeader
{

    /**
     * @var string
     */
    const COLUMN_NAME = 'tx_yoastseo_focuskeyword';

    /**
     * @var CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * Initialize the page renderer
     */
    public function __construct()
    {
        $this->pageRenderer = CMS\Core\Utility\GeneralUtility::makeInstance(CMS\Core\Page\PageRenderer::class);
    }

    /**
     * @return string
     */
    public function render()
    {
        $lineBuffer = array();

        $queryParameters = CMS\Core\Utility\GeneralUtility::_GET();

        $currentPage = NULL;
        $focusKeyword = '';
        $previewDataUrl = '';
        $recordId = 0;

        if (is_array($queryParameters) && array_key_exists('id', $queryParameters) && !empty($queryParameters['id'])) {
            $currentPage = CMS\Backend\Utility\BackendUtility::getRecord('pages', (int) $queryParameters['id']);
        }

        if (is_array($currentPage) && array_key_exists(self::COLUMN_NAME, $currentPage)) {
            $focusKeyword = $currentPage[self::COLUMN_NAME];

            $recordId = $currentPage['uid'];

            $previewDataUrl = CMS\Backend\Utility\BackendUtility::getModuleUrl(
                'ajax_yoast_seo-page-content-preview',
                array(
                    'yoast' => array(
                        'preview' => array(
                            'page' => $currentPage['uid']
                        )
                    )
                )
            );
        }

        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/YoastSeo/bundle');

        $this->pageRenderer->addCssFile(
            CMS\Core\Utility\ExtensionManagementUtility::extRelPath('yoast_seo') . 'Resources/Public/CSS/yoast-seo.min.css'
        );

        $lineBuffer[] = '<div id="snippet" ' .
            'data-yoast-focuskeyword="' . htmlspecialchars($focusKeyword) . '"' .
            'data-yoast-previewdataurl="' . htmlspecialchars($previewDataUrl) . '"' .
            'data-yoast-recordid="' . htmlspecialchars($recordId) . '"' .
            '></div>';

        $lineBuffer[] = '<div class="yoastPanel">';
        $lineBuffer[] = '<h3 class="snippet-editor__heading" data-controls="readability">';
		$lineBuffer[] = '<span class="wpseo-score-icon"></span> Readability <span class="fa fa-chevron-down"></span>';
		$lineBuffer[] = '</h3>';
        $lineBuffer[] = '<div id="readability" class="yoastPanel__content"></div>';
        $lineBuffer[] = '</div>';

        $lineBuffer[] = '<div class="yoastPanel">';
		$lineBuffer[] = '<h3 class="snippet-editor__heading" data-controls="seo">';
        $lineBuffer[] = '<span class="wpseo-score-icon"></span> SEO <span class="fa fa-chevron-down"></span>';
		$lineBuffer[] = '</h3>';
        $lineBuffer[] = '<div id="seo" class="yoastPanel__content"></div>';
        $lineBuffer[] = '</div>';

        return implode(PHP_EOL, $lineBuffer);
    }

}