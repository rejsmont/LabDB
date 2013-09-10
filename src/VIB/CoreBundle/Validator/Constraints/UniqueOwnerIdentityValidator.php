<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace VIB\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Unique owner identity validator for ACL
 * 
 * @Annotation
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class UniqueOwnerIdentityValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $count = 0;
        foreach ($value as $entry) {
            if ($entry['permission'] == MaskBuilder::MASK_OWNER) {
                $count++;
                if ($count > 1) {
                    $this->context->addViolation($constraint->message);
                    break;
                }
            }
        }
    }
}

?>
