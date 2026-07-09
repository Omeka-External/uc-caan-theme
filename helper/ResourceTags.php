<?php 
namespace OmekaTheme\Helper;

use Laminas\View\Helper\AbstractHelper;

class ResourceTags extends AbstractHelper
{
    /**
     * @var string[]|null Cached list of tag colors (hex, e.g. '#566551').
     */
    private static $colorList = null;

    /**
     * Returns a Resource Tag HTML.
     *
     * @param object $resource The resource to add a tag to.
     * @return string
     */
    public function __invoke($resource)
    {
        if (!$resource) {
            return '';
        }

        $view = $this->getView();

        $resource_tags = $view->themeSetting('resource_tags');

        $tagsHtml = '';

        if (is_array($resource_tags) && (in_array('resource_type', $resource_tags) || in_array('resource_class', $resource_tags))) {

            $tagsHtml .= '<div class="resource-tags">';

            // Resource Type Tag ('Item', 'Item set', 'Media').

            if (in_array('resource_type', $resource_tags)) {

                $resourceName = $resource->resourceName();

                if ($resourceName) {
                    $mapResourceName = array(
                        'items' => array(
                            'id' => 0,
                            'label' => 'Item'
                        ),
                        'item_sets' => array(
                            'id' => 7,
                            'label' => 'Item set'
                        ),
                        'media' => array(
                            'id' => 3,
                            'label' => 'Media'
                        )
                    );
    
                    if (array_key_exists($resourceName, $mapResourceName)) {
                        $tagColor = $this->getUniqueColorFromId($mapResourceName[$resourceName]['id'], 0.2);
                        $tagColorSolid = $this->getUniqueColorFromId($mapResourceName[$resourceName]['id']);
                        $tagLabel = $mapResourceName[$resourceName]['label'];
                        $tagsHtml .= '<div class="resource-tag" style="background-color: ' . $tagColor . ';"><span class="resource-tag-color" style="background-color: ' . $tagColorSolid . ';"></span>' . $tagLabel . '</div>';
                    }
                }
            }

            

            // Resource Class Tag.

            if (in_array('resource_class', $resource_tags)) {
                
                $resourceClass = $resource->resourceClass();

                if ($resourceClass) {
                    $resourceClassId = $resourceClass->id();

                    if ($resourceClassId) {
                        $tagColor = $this->getUniqueColorFromId((int) $resourceClassId + 10, 0.2); // Offset of 10 for Resource Types.
                        $tagColorSolid = $this->getUniqueColorFromId((int) $resourceClassId + 10);
                        $tagLabel = $resource->displayResourceClassLabel();
                        $tagsHtml .= '<div class="resource-tag" style="background-color: ' . $tagColor . ';"><span class="resource-tag-color" style="background-color: ' . $tagColorSolid . ';"></span>' . $tagLabel . '</div>';
                    }
                }
            }

            $tagsHtml .= '</div>';
        }

        return $tagsHtml;
    }

    /**
     * Returns a color assigned to an ID, picked from the theme's tag color list.
     *
     * The same ID always maps to the same color (and stays fixed across page
     * reloads), since the color is picked deterministically from the ID.
     *
     * @param int $n A unique ID.
     * @param float $alpha Opacity from 0 (transparent) to 1 (opaque).
     * @return string An rgba() color, or an empty string if the color list is
     *  missing or empty.
     */
    private function getUniqueColorFromId($n, $alpha = 1)
    {
        $colors = $this->getColorList();

        if (!$colors) {
            return '';
        }

        $index = crc32((string) $n) % count($colors);

        return $this->hexToRgba($colors[$index], $alpha);
    }

    /**
     * Converts a '#rrggbb' hex color to an 'rgba(r, g, b, a)' string.
     *
     * @param string $hex A hex color, e.g. '#566551'.
     * @param float $alpha Opacity from 0 (transparent) to 1 (opaque).
     * @return string
     */
    private function hexToRgba($hex, $alpha = 1)
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }

    /**
     * Returns the theme's tag color list, loaded from JSON and cached for
     * the lifetime of the request.
     *
     * @return string[] Hex colors (e.g. '#566551').
     */
    private function getColorList()
    {
        if (self::$colorList === null) {
            $path = __DIR__ . '/../asset/data/resource-tag-colors.json';
            $json = is_readable($path) ? file_get_contents($path) : false;
            $colors = $json ? json_decode($json, true) : null;
            self::$colorList = is_array($colors) ? $colors : [];
        }

        return self::$colorList;
    }
}
