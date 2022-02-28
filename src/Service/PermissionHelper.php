<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;

/**
 * Class PermissionHelper
 * 
 * PermissionHelper formates query with permissions parameters.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class PermissionHelper
{
    const PUBLIC_ACCESS = 'PUBLIC_ACCESS';

    /**
     * Inserts permissions restrictions to a query.
     */
    public function setPermissions(QueryBuilder $builder, array $permissions = []): QueryBuilder
    {
        if(empty($permissions)) {
            $permissions = [self::PUBLIC_ACCESS];
        }

        $orStatements = $builder->expr()->orX();
        $i = 0;
        foreach ($permissions as $permission) {
            $i++;
            $orStatements->add(
                $builder->expr()->like('c.permissions', $builder->expr()->literal('%' . $permission . '%'))
            );
        }
        $builder->andWhere($orStatements);
        return $builder;
    }
}