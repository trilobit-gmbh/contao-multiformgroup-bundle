<?php

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-multiformgroup-bundle
 */

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('multi_form_group_legend', 'expert_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_BEFORE, true)
    ->addField(['multi_form_group'], 'multi_form_group_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('fieldsetfsStart', 'tl_form_field')
;

$GLOBALS['TL_DCA']['tl_form_field']['fields']['multi_form_group'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_form_field']['multi_form_group'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'sql' => "char(1) NOT NULL default ''",
];
