<?php

declare(strict_types=1);

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-multiformgroup-bundle
 */

use Trilobit\ContaoMultiFormGroup\Form\FormCompiler;

$GLOBALS['TL_HOOKS']['compileFormFields'][] = [FormCompiler::class, 'onCompileFormFields'];

if ('FE' === TL_MODE) {
    \Contao\System::loadLanguageFile('default');

    $GLOBALS['TL_HEAD'][] = '<script>var multi_form_control_add_label = "'.$GLOBALS['TL_LANG']['MSC']['multi_form_control_add'].'";</script>';
    $GLOBALS['TL_HEAD'][] = '<script>var multi_form_control_remove_label = "'.$GLOBALS['TL_LANG']['MSC']['multi_form_control_remove'].'";</script>';
    $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/trilobitcontaomultiformgroup/js/frontend.js';
}
