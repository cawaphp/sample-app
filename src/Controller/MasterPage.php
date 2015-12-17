<?php
declare (strict_types=1);

namespace SampleApp\Controller;

use Cawa\Core\Controller\Renderer\HtmlPage;
use Cawa\Core\Controller\Renderer\Phtml;

/**
 */
class MasterPage extends HtmlPage
{
    use Phtml {
        Phtml::render as private phtmlRender;
    }

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $css = <<<EOF
body {
  padding-top: 70px;
}
footer {
  padding: 30px 0;
}
EOF;

        $this->addCss($css);
    }

    /**
     * @param string $main
     *
     * @return $this
     */
    public function setMain(string $main) : self
    {
        $this->data['main'] = $main;

        return $this;
    }

    /**
     * @param string $aside
     *
     * @return $this
     */
    public function setAside(string $aside) : self
    {
        $this->data['aside'] = $aside;

        return $this;
    }

    /**
     * @return string
     */
    public function render() : string
    {
        $this->getBody()->setContent($this->phtmlRender());

        return parent::render();
    }
}
