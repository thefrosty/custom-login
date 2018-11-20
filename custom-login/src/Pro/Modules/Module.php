<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Pro\Modules;

use TheFrosty\WpUtilities\Models\BaseModel;

/**
 * Class Module.
 * @package TheFrosty\CustomLogin\Pro\Modules
 */
class Module extends BaseModel
{
    const DESCRIPTION = 'description';
    const FULLY_QUALIFIED_CLASS = 'fully_qualified_class';
    const IMAGE = 'image';
    const TITLE = 'title';

    /**
     * The Modules description.
     * @var string $description
     */
    private $description;

    /**
     * The Modules fully qualified class.
     * @var string $fully_qualified_class
     */
    private $fully_qualified_class;

    /**
     * The Modules image src.
     * @var string $image
     */
    private $image;

    /**
     * The Modules setting title.
     * @var string $title
     */
    private $title;

    /**
     * Set the Modules setting title.
     * @param string $description
     */
    public function setDescription(string $description = '')
    {
        $this->description = $description;
    }

    /**
     * Get the Modules setting title.
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * Set the Modules fully qualified class.
     * @param string $fully_qualified_class
     */
    public function setFullyQualifiedClass(string $fully_qualified_class)
    {
        $this->fully_qualified_class = $fully_qualified_class;
    }

    /**
     * Get the Modules fully qualified class.
     * @return string
     */
    public function getFullyQualifiedClass(): string
    {
        return $this->fully_qualified_class;
    }

    /**
     * Set the Modules image src.
     * @param string $image
     */
    public function setImage(string $image = '')
    {
        $this->image = $image;
    }

    /**
     * Get the Modules image src.
     * @return string
     */
    public function getImage(): string
    {
        return $this->image ?? '';
    }

    /**
     * Set the Modules setting title.
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Get the Modules setting title.
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
