<?php

namespace VIB\SecurityBundle\Bridge\Doctrine;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;


/**
 * Description of ACLHelper
 *
 * @link https://gist.github.com/mailaneel/1363377 Original code on gist
 * 
 * @author mailaneel
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AclHelper
{
    /**
     * 
     * @param type $doctrine
     * @param type $securityContext
     */
    function __construct($doctrine, $securityContext, $options = array())
    {
        $this->em = $doctrine->getEntityManager();
        $this->securityContext = $securityContext;
        $this->aclConnection = $doctrine->getConnection('default');
        $this->aclWalker = $options[0];
        $this->aclClassMapping = $options[1];
        $this->roleHierarchy = $options[2];
    }
 
    /**
     * 
     * @param \Doctrine\ORM\Query $query
     * @return \Doctrine\ORM\Query
     */
    protected function cloneQuery(Query $query)
    {
        $aclAppliedQuery = clone $query;
        $params = $query->getParameters();
        foreach ($params as $key => $param) {
            $aclAppliedQuery->setParameter($key, $param);
        }
 
        return $query;
    }
 
    /**
     * Get parent roles of specified role
     * 
     * @param string $role
     * @return array
     */
    protected function resolveRoles($role) {
        $hierarchy = $this->roleHierarchy;
        $roles = array();
        if (array_key_exists($role, $hierarchy)) {
            foreach($hierarchy[$role] as $parent_role) {
                $roles[] = '"' . $parent_role . '"';
                $roles = array_merge($roles,$this->resolveRoles($parent_role));
            }
        }
        return $roles;
    }
    
    /**
     * 
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $permissions
     * @return \Doctrine\ORM\Query
     */
    public function apply(QueryBuilder $queryBuilder,
            array $permissions = array("VIEW"), $user = null)
    {
 
        $whereQueryParts = $queryBuilder->getDQLPart('where');
        if (empty($whereQueryParts)) {
            $fromQueryParts = $queryBuilder->getDQLPart('from');
            $firstFromQueryAlias = $fromQueryParts[0]->getAlias();
            $queryBuilder->where($firstFromQueryAlias . '.id > 0'); // this will help in cases where no where query is specified, where query is required to walk in where clause
        }
 
        $query = $this->cloneQuery($queryBuilder->getQuery());
 
 
        $builder = new MaskBuilder();
        foreach ($permissions as $permission) {
            $mask = constant(get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }
        $query->setHint('acl.mask', $builder->get());
 
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER,$this->aclWalker);
        $entities = $queryBuilder->getRootEntities();
        $query->setHint('acl.root.entities', $entities);
 
        $query->setHint('acl.extra.query',
                $this->getPermittedIdsACLSQLForUser($query, $queryBuilder, $user));
 
        $class = $this->em->getClassMetadata($entities[0]);
        $entityRootTableName = $class->getQuotedTableName($this->em->getConnection()->getDatabasePlatform());
        $entityRootAlias = $queryBuilder->getRootAlias();
 
        $query->setHint('acl.entityRootTableName', $entityRootTableName);
        $query->setHint('acl.entityRootTableDqlAlias', $entityRootAlias);
 
        return $query;
    }
 
    /**
     * 
     * @param \Doctrine\ORM\Query $query
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return string
     */
    private function getPermittedIdsACLSQLForUser(Query $query,
            QueryBuilder $queryBuilder, $user = null)
    {
        $database = $this->aclConnection->getDatabase();
        $mask = $query->getHint('acl.mask');
        $rootEntities = $query->getHint('acl.root.entities');
        foreach ($rootEntities as $rootEntity) {
            $rE[] = '"' . str_replace('\\', '\\\\', $rootEntity) . '"';
            if (array_key_exists($rootEntity, $this->aclClassMapping)) {
                foreach ($this->aclClassMapping[$rootEntity] as $subClass) {
                    $rE[] = '"' . str_replace('\\', '\\\\', $subClass) . '"';
                }
            }
            break;
        }
        $INentities = implode(',', $rE);
 
        if (null === $user) {
            $token = $this->securityContext->getToken(); // for now lets imagine we will have token i.e user is logged in
            $user = $token->getUser();
        }
        $INroles = "''";
 
        if (is_object($user)) {
            $userRoles = $user->getRoles();
            $uR = array();
            foreach ($userRoles as $role) {
                $uR[] = '"' . $role . '"';
                $uR = array_merge($uR,$this->resolveRoles($role));
            }
            $uR = array_unique($uR);
            $uR[] = '"' . str_replace('\\', '\\\\', get_class($user)) . '-' . $user->getUserName() . '"';
            $INroles = implode(',', $uR);
        }
        
        $selectQuery = <<<SELECTQUERY
          SELECT DISTINCT o.object_identifier as id FROM {$database}.acl_object_identities as o 
          INNER JOIN {$database}.acl_classes c ON c.id = o.class_id
          LEFT JOIN {$database}.acl_entries e ON (
                e.class_id = o.class_id AND (e.object_identity_id = o.id OR {$this->aclConnection->getDatabasePlatform()->getIsNullExpression('e.object_identity_id')})
            )
         LEFT JOIN {$database}.acl_security_identities s ON (
                s.id = e.security_identity_id
            )
          WHERE  c.class_type IN ({$INentities})
          AND s.identifier IN ({$INroles})
          AND e.mask >= {$mask} 
        
SELECTQUERY;
 
        return $selectQuery;
    }
}
 
?>