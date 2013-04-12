<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * Original code by mailaneel is available at
 * https://gist.github.com/mailaneel/1363377
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

namespace VIB\SecurityBundle\Bridge\Doctrine;

use Doctrine\ORM\Query\SqlWalker;


/**
 * The AclWalker is a TreeWalker that walks over a DQL AST and constructs
 * the corresponding SQL.
 *
 * @link https://gist.github.com/mailaneel/1363377 Original code on gist
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * @author mailaneel
 */
class AclWalker extends SqlWalker
{    
    /**
     * {@inheritdoc}
     */
    public function walkFromClause($fromClause)
    {
        $sql = parent::walkFromClause($fromClause);
        $tableAlias = $this->getSQLTableAlias($this->getQuery()->getHint('acl.entityRootTableName'),
                                              $this->getQuery()->getHint('acl.entityRootTableDqlAlias'));
        $extraQuery = $this->getQuery()->getHint('acl.extra.query');


        $tempAclView = <<<tempAclView
        JOIN ({$extraQuery}) ta_ ON {$tableAlias}.id = ta_.id
tempAclView;

        return $sql . $tempAclView;
    }

}

?>
