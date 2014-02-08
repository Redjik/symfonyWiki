<?php

namespace Redjik\WikiBundle\Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Redjik\WikiBundle\Model\Pages;
use Redjik\WikiBundle\Model\PagesPeer;
use Redjik\WikiBundle\Model\PagesQuery;

/**
 * @method PagesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method PagesQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method PagesQuery orderByText($order = Criteria::ASC) Order by the text column
 * @method PagesQuery orderByAlias($order = Criteria::ASC) Order by the alias column
 * @method PagesQuery orderByFullpath($order = Criteria::ASC) Order by the fullpath column
 * @method PagesQuery orderByParent($order = Criteria::ASC) Order by the parent column
 *
 * @method PagesQuery groupById() Group by the id column
 * @method PagesQuery groupByTitle() Group by the title column
 * @method PagesQuery groupByText() Group by the text column
 * @method PagesQuery groupByAlias() Group by the alias column
 * @method PagesQuery groupByFullpath() Group by the fullpath column
 * @method PagesQuery groupByParent() Group by the parent column
 *
 * @method PagesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PagesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PagesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PagesQuery leftJoinPagesRelatedByParent($relationAlias = null) Adds a LEFT JOIN clause to the query using the PagesRelatedByParent relation
 * @method PagesQuery rightJoinPagesRelatedByParent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PagesRelatedByParent relation
 * @method PagesQuery innerJoinPagesRelatedByParent($relationAlias = null) Adds a INNER JOIN clause to the query using the PagesRelatedByParent relation
 *
 * @method PagesQuery leftJoinPagesRelatedById($relationAlias = null) Adds a LEFT JOIN clause to the query using the PagesRelatedById relation
 * @method PagesQuery rightJoinPagesRelatedById($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PagesRelatedById relation
 * @method PagesQuery innerJoinPagesRelatedById($relationAlias = null) Adds a INNER JOIN clause to the query using the PagesRelatedById relation
 *
 * @method Pages findOne(PropelPDO $con = null) Return the first Pages matching the query
 * @method Pages findOneOrCreate(PropelPDO $con = null) Return the first Pages matching the query, or a new Pages object populated from the query conditions when no match is found
 *
 * @method Pages findOneByTitle(string $title) Return the first Pages filtered by the title column
 * @method Pages findOneByText(string $text) Return the first Pages filtered by the text column
 * @method Pages findOneByAlias(string $alias) Return the first Pages filtered by the alias column
 * @method Pages findOneByFullpath(string $fullpath) Return the first Pages filtered by the fullpath column
 * @method Pages findOneByParent(int $parent) Return the first Pages filtered by the parent column
 *
 * @method Pages[] findById(int $id) Return Pages objects filtered by the id column
 * @method Pages[] findByTitle(string $title) Return Pages objects filtered by the title column
 * @method Pages[] findByText(string $text) Return Pages objects filtered by the text column
 * @method Pages[] findByAlias(string $alias) Return Pages objects filtered by the alias column
 * @method Pages[] findByFullpath(string $fullpath) Return Pages objects filtered by the fullpath column
 * @method Pages[] findByParent(int $parent) Return Pages objects filtered by the parent column
 */
abstract class BasePagesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePagesQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'default';
        }
        if (null === $modelName) {
            $modelName = 'Redjik\\WikiBundle\\Model\\Pages';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PagesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PagesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PagesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PagesQuery) {
            return $criteria;
        }
        $query = new PagesQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Pages|Pages[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PagesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Pages A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Pages A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `title`, `text`, `alias`, `fullpath`, `parent` FROM `pages` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Pages();
            $obj->hydrate($row);
            PagesPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Pages|Pages[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Pages[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return PagesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PagesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PagesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PagesPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PagesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PagesPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PagesPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagesPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PagesQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PagesPeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the text column
     *
     * Example usage:
     * <code>
     * $query->filterByText('fooValue');   // WHERE text = 'fooValue'
     * $query->filterByText('%fooValue%'); // WHERE text LIKE '%fooValue%'
     * </code>
     *
     * @param     string $text The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PagesQuery The current query, for fluid interface
     */
    public function filterByText($text = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($text)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $text)) {
                $text = str_replace('*', '%', $text);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PagesPeer::TEXT, $text, $comparison);
    }

    /**
     * Filter the query on the alias column
     *
     * Example usage:
     * <code>
     * $query->filterByAlias('fooValue');   // WHERE alias = 'fooValue'
     * $query->filterByAlias('%fooValue%'); // WHERE alias LIKE '%fooValue%'
     * </code>
     *
     * @param     string $alias The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PagesQuery The current query, for fluid interface
     */
    public function filterByAlias($alias = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($alias)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $alias)) {
                $alias = str_replace('*', '%', $alias);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PagesPeer::ALIAS, $alias, $comparison);
    }

    /**
     * Filter the query on the fullpath column
     *
     * Example usage:
     * <code>
     * $query->filterByFullpath('fooValue');   // WHERE fullpath = 'fooValue'
     * $query->filterByFullpath('%fooValue%'); // WHERE fullpath LIKE '%fooValue%'
     * </code>
     *
     * @param     string $fullpath The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PagesQuery The current query, for fluid interface
     */
    public function filterByFullpath($fullpath = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($fullpath)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $fullpath)) {
                $fullpath = str_replace('*', '%', $fullpath);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PagesPeer::FULLPATH, $fullpath, $comparison);
    }

    /**
     * Filter the query on the parent column
     *
     * Example usage:
     * <code>
     * $query->filterByParent(1234); // WHERE parent = 1234
     * $query->filterByParent(array(12, 34)); // WHERE parent IN (12, 34)
     * $query->filterByParent(array('min' => 12)); // WHERE parent >= 12
     * $query->filterByParent(array('max' => 12)); // WHERE parent <= 12
     * </code>
     *
     * @see       filterByPagesRelatedByParent()
     *
     * @param     mixed $parent The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PagesQuery The current query, for fluid interface
     */
    public function filterByParent($parent = null, $comparison = null)
    {
        if (is_array($parent)) {
            $useMinMax = false;
            if (isset($parent['min'])) {
                $this->addUsingAlias(PagesPeer::PARENT, $parent['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parent['max'])) {
                $this->addUsingAlias(PagesPeer::PARENT, $parent['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagesPeer::PARENT, $parent, $comparison);
    }

    /**
     * Filter the query by a related Pages object
     *
     * @param   Pages|PropelObjectCollection $pages The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PagesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPagesRelatedByParent($pages, $comparison = null)
    {
        if ($pages instanceof Pages) {
            return $this
                ->addUsingAlias(PagesPeer::PARENT, $pages->getId(), $comparison);
        } elseif ($pages instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PagesPeer::PARENT, $pages->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPagesRelatedByParent() only accepts arguments of type Pages or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PagesRelatedByParent relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PagesQuery The current query, for fluid interface
     */
    public function joinPagesRelatedByParent($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PagesRelatedByParent');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'PagesRelatedByParent');
        }

        return $this;
    }

    /**
     * Use the PagesRelatedByParent relation Pages object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Redjik\WikiBundle\Model\PagesQuery A secondary query class using the current class as primary query
     */
    public function usePagesRelatedByParentQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPagesRelatedByParent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PagesRelatedByParent', '\Redjik\WikiBundle\Model\PagesQuery');
    }

    /**
     * Filter the query by a related Pages object
     *
     * @param   Pages|PropelObjectCollection $pages  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PagesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPagesRelatedById($pages, $comparison = null)
    {
        if ($pages instanceof Pages) {
            return $this
                ->addUsingAlias(PagesPeer::ID, $pages->getParent(), $comparison);
        } elseif ($pages instanceof PropelObjectCollection) {
            return $this
                ->usePagesRelatedByIdQuery()
                ->filterByPrimaryKeys($pages->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPagesRelatedById() only accepts arguments of type Pages or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PagesRelatedById relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PagesQuery The current query, for fluid interface
     */
    public function joinPagesRelatedById($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PagesRelatedById');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'PagesRelatedById');
        }

        return $this;
    }

    /**
     * Use the PagesRelatedById relation Pages object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Redjik\WikiBundle\Model\PagesQuery A secondary query class using the current class as primary query
     */
    public function usePagesRelatedByIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPagesRelatedById($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PagesRelatedById', '\Redjik\WikiBundle\Model\PagesQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Pages $pages Object to remove from the list of results
     *
     * @return PagesQuery The current query, for fluid interface
     */
    public function prune($pages = null)
    {
        if ($pages) {
            $this->addUsingAlias(PagesPeer::ID, $pages->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
