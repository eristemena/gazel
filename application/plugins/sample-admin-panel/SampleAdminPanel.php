<?php

require_once "Gazel/Plugin/Abstract.php";

class SampleAdminPanelPlugin extends Gazel_Plugin_Abstract
{
	/**
     * When frontend render page head (<head></head>)
     *
     * @param string $head Head content
     * @return string
     */
    public function onFrontendRenderHead($head)
    {
        $options = $this->getPluginOptions();

        if( $options['message'] )
        {
            $head .= '<script>window.onload=function(){alert("'.$options['message'].'")}</script>';
        }
        
        return $head;
    }

    /**
     * Render admin panel
     *
     * @params Gazel_Form $form Form to render
     * @return Gazel_Form
     */
    public function onAdminRenderPanel(Gazel_Form $form)
    {
        $form->addElement('text', 'message', array(
            'label' => 'Message',
            'size' => '45'
        ));

        return $form;
    }
}
