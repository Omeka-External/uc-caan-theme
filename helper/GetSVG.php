<?php 
namespace OmekaTheme\Helper;

use Laminas\View\Helper\AbstractHelper;

class GetSVG extends AbstractHelper
{

    /**
     * Get SVG Markup.
     *
     * @param string $name The SVG name.
     * @return string
     */
    public function __invoke($name)
    {
        if (!$name) {
            return '';
        }

        $view = $this->getView();
        
        $filePath = OMEKA_PATH . '/themes/uc-caan/asset/img/' . $name . '.svg';
        return file_get_contents($filePath);
    }
}
