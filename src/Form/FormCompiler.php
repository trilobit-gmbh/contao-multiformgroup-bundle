<?php

declare(strict_types=1);

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-multiformgroup-bundle
 */

namespace Trilobit\ContaoMultiFormGroup\Form;

use Contao\Form;
use Contao\FormFieldModel;
use Contao\Input;

class FormCompiler
{
    /**
     * @param FormFieldModel[] $currentFieldsList
     * @param string           $formFieldId
     * @param Form             $form
     *
     * @return FormFieldModel[]
     */
    public function onCompileFormFields(array $currentFieldsList, $formFieldId, $form)
    {
        $offset = 0;
        $indexFrom = 0;
        $level = 0;

        $defaultFieldCount = count(array_values($currentFieldsList));

        $newFieldsList = [];
        $multiFormGroupFields = [];

        foreach (array_values($currentFieldsList) as $index => $fieldModel) {
            if ('fieldsetStart' === $fieldModel->type
                || ('fieldset' === $fieldModel->type && 'fsStart' === $fieldModel->fsType)
            ) {
                ++$level;
            }

            if (0 === $level) {
                $newFieldsList[] = $fieldModel;
            } else {
                $multiFormGroupFields[] = $fieldModel;
            }

            if ('fieldsetStop' === $fieldModel->type
                || ('fieldset' === $fieldModel->type && 'fsStop' === $fieldModel->fsType)
            ) {
                --$level;

                if (0 === $level) {
                    $newFieldsList = array_merge(
                        $newFieldsList,
                        $this->multiplyFields(
                            $multiFormGroupFields
                        )
                    );

                    $multiFormGroupFields = [];
                }
            }
        }

        return $newFieldsList;
    }

    /**
     * @param $fields array The fields [<, A, B, C, >] to multiply (first and last element must be a fieldset)
     *
     * @return array an array containing the multiplied sequence with adapted identifiers
     *               [<, A1, B1, C1, >, <, A2, B2, C2, >, <, A3, B3, C3, >]
     */
    private function multiplyFields($fields)
    {
        if (!array_key_exists(0, $fields)) {
            return [];
        }

        $groupId = $fields[0]->id;

        // tag first field
        $fields[0]->class = 'multi_form_group multi_form_group__'.$groupId;

        $groupCount = $this->getGroupCount($groupId);

        $multipliedFields = [];

        for ($i = 0; $i < $groupCount; ++$i) {
            foreach ($fields as $field) {
                $multipliedField = clone $field;

                // add suffix
                $multipliedField->name = $this->getSuffixedIdentifier($field->name, (string) $i);
                $multipliedField->id = $this->getSuffixedIdentifier($field->name, (string) $i);
                $multipliedField->baseId = $field->id;

                $multipliedFields[] = $multipliedField;
            }
        }

        return $multipliedFields;
    }

    /**
     * Return the number of groups to be shown.
     *
     * @param $groupId
     *
     * @return int
     */
    private function getGroupCount($groupId)
    {
        $size = (int) Input::post('multi_form_size__'.$groupId);

        return max($size, 1);
    }

    /**
     * Helper: Generate the suffixed identifier.
     *
     * @param $name
     * @param $groupSuffix
     *
     * @return string
     */
    private function getSuffixedIdentifier($name, $groupSuffix)
    {
        return $name.'__'.$groupSuffix;
    }
}
