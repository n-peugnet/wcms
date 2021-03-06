<?php

namespace Wcms;

class Controllerfont extends Controller
{
    /**
     * @var Modelfont
     */
    protected $fontmanager;
    
    public function __construct($router)
    {
        parent::__construct($router);
        $this->fontmanager = new Modelfont();
    }

    public function desktop()
    {
        if ($this->user->iseditor()) {
            dircheck(Model::FONT_DIR);

            $fontlist = $this->fontmanager->getfontlist();
            
            $this->showtemplate(
                'font',
                [
                    'fontlist' => $fontlist,
                    'fonttypes' => $this->fontmanager->getfonttypes(),
                    'fontfile' => Model::dirtopath(Model::ASSETS_CSS_DIR) . 'fonts.css'
                ]
            );
        } else {
            $this->routedirect('home');
        }
    }
    
    public function render()
    {
        $this->fontmanager->renderfontface();
        $this->routedirect('font');
    }

    public function add()
    {
        if (isset($_POST['fontname'])) {
            $fontname = $_POST['fontname'];
        } else {
            $fontname = '';
        }
        $message = $this->fontmanager->upload($_FILES, 2 ** 16, $fontname);
        if ($message !== true) {
            echo $message;
        } else {
            $this->render();
        }
    }
}
