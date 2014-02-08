<?php

namespace Redjik\WikiBundle\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Redjik\WikiBundle\Model\Pages;
use Redjik\WikiBundle\Model\PagesPeer;
use Redjik\WikiBundle\Model\PagesQuery;

abstract class BasePages extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Redjik\\WikiBundle\\Model\\PagesPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PagesPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the text field.
     * @var        string
     */
    protected $text;

    /**
     * The value for the alias field.
     * @var        string
     */
    protected $alias;

    /**
     * The value for the fullpath field.
     * @var        string
     */
    protected $fullpath;

    /**
     * The value for the parent field.
     * @var        int
     */
    protected $parent;

    /**
     * @var        Pages
     */
    protected $aPagesRelatedByParent;

    /**
     * @var        PropelObjectCollection|Pages[] Collection to store aggregation of Pages objects.
     */
    protected $collPagessRelatedById;
    protected $collPagessRelatedByIdPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $pagessRelatedByIdScheduledForDeletion = null;

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [text] column value.
     *
     * @return string
     */
    public function getText()
    {

        return $this->text;
    }

    /**
     * Get the [alias] column value.
     *
     * @return string
     */
    public function getAlias()
    {

        return $this->alias;
    }

    /**
     * Get the [fullpath] column value.
     *
     * @return string
     */
    public function getFullpath()
    {

        return $this->fullpath;
    }

    /**
     * Get the [parent] column value.
     *
     * @return int
     */
    public function getParent()
    {

        return $this->parent;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Pages The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = PagesPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return Pages The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = PagesPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [text] column.
     *
     * @param  string $v new value
     * @return Pages The current object (for fluent API support)
     */
    public function setText($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->text !== $v) {
            $this->text = $v;
            $this->modifiedColumns[] = PagesPeer::TEXT;
        }


        return $this;
    } // setText()

    /**
     * Set the value of [alias] column.
     *
     * @param  string $v new value
     * @return Pages The current object (for fluent API support)
     */
    public function setAlias($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->alias !== $v) {
            $this->alias = $v;
            $this->modifiedColumns[] = PagesPeer::ALIAS;
        }


        return $this;
    } // setAlias()

    /**
     * Set the value of [fullpath] column.
     *
     * @param  string $v new value
     * @return Pages The current object (for fluent API support)
     */
    public function setFullpath($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->fullpath !== $v) {
            $this->fullpath = $v;
            $this->modifiedColumns[] = PagesPeer::FULLPATH;
        }


        return $this;
    } // setFullpath()

    /**
     * Set the value of [parent] column.
     *
     * @param  int $v new value
     * @return Pages The current object (for fluent API support)
     */
    public function setParent($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->parent !== $v) {
            $this->parent = $v;
            $this->modifiedColumns[] = PagesPeer::PARENT;
        }

        if ($this->aPagesRelatedByParent !== null && $this->aPagesRelatedByParent->getId() !== $v) {
            $this->aPagesRelatedByParent = null;
        }


        return $this;
    } // setParent()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->title = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->text = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->alias = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->fullpath = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->parent = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 6; // 6 = PagesPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Pages object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

        if ($this->aPagesRelatedByParent !== null && $this->parent !== $this->aPagesRelatedByParent->getId()) {
            $this->aPagesRelatedByParent = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(PagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PagesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aPagesRelatedByParent = null;
            $this->collPagessRelatedById = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(PagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PagesQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(PagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                PagesPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aPagesRelatedByParent !== null) {
                if ($this->aPagesRelatedByParent->isModified() || $this->aPagesRelatedByParent->isNew()) {
                    $affectedRows += $this->aPagesRelatedByParent->save($con);
                }
                $this->setPagesRelatedByParent($this->aPagesRelatedByParent);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->pagessRelatedByIdScheduledForDeletion !== null) {
                if (!$this->pagessRelatedByIdScheduledForDeletion->isEmpty()) {
                    PagesQuery::create()
                        ->filterByPrimaryKeys($this->pagessRelatedByIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->pagessRelatedByIdScheduledForDeletion = null;
                }
            }

            if ($this->collPagessRelatedById !== null) {
                foreach ($this->collPagessRelatedById as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = PagesPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PagesPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PagesPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(PagesPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(PagesPeer::TEXT)) {
            $modifiedColumns[':p' . $index++]  = '`text`';
        }
        if ($this->isColumnModified(PagesPeer::ALIAS)) {
            $modifiedColumns[':p' . $index++]  = '`alias`';
        }
        if ($this->isColumnModified(PagesPeer::FULLPATH)) {
            $modifiedColumns[':p' . $index++]  = '`fullpath`';
        }
        if ($this->isColumnModified(PagesPeer::PARENT)) {
            $modifiedColumns[':p' . $index++]  = '`parent`';
        }

        $sql = sprintf(
            'INSERT INTO `pages` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`title`':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '`text`':
                        $stmt->bindValue($identifier, $this->text, PDO::PARAM_STR);
                        break;
                    case '`alias`':
                        $stmt->bindValue($identifier, $this->alias, PDO::PARAM_STR);
                        break;
                    case '`fullpath`':
                        $stmt->bindValue($identifier, $this->fullpath, PDO::PARAM_STR);
                        break;
                    case '`parent`':
                        $stmt->bindValue($identifier, $this->parent, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aPagesRelatedByParent !== null) {
                if (!$this->aPagesRelatedByParent->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPagesRelatedByParent->getValidationFailures());
                }
            }


            if (($retval = PagesPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collPagessRelatedById !== null) {
                    foreach ($this->collPagessRelatedById as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = PagesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getTitle();
                break;
            case 2:
                return $this->getText();
                break;
            case 3:
                return $this->getAlias();
                break;
            case 4:
                return $this->getFullpath();
                break;
            case 5:
                return $this->getParent();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Pages'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Pages'][$this->getPrimaryKey()] = true;
        $keys = PagesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getText(),
            $keys[3] => $this->getAlias(),
            $keys[4] => $this->getFullpath(),
            $keys[5] => $this->getParent(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aPagesRelatedByParent) {
                $result['PagesRelatedByParent'] = $this->aPagesRelatedByParent->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collPagessRelatedById) {
                $result['PagessRelatedById'] = $this->collPagessRelatedById->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = PagesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setTitle($value);
                break;
            case 2:
                $this->setText($value);
                break;
            case 3:
                $this->setAlias($value);
                break;
            case 4:
                $this->setFullpath($value);
                break;
            case 5:
                $this->setParent($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = PagesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setTitle($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setText($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setAlias($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setFullpath($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setParent($arr[$keys[5]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PagesPeer::DATABASE_NAME);

        if ($this->isColumnModified(PagesPeer::ID)) $criteria->add(PagesPeer::ID, $this->id);
        if ($this->isColumnModified(PagesPeer::TITLE)) $criteria->add(PagesPeer::TITLE, $this->title);
        if ($this->isColumnModified(PagesPeer::TEXT)) $criteria->add(PagesPeer::TEXT, $this->text);
        if ($this->isColumnModified(PagesPeer::ALIAS)) $criteria->add(PagesPeer::ALIAS, $this->alias);
        if ($this->isColumnModified(PagesPeer::FULLPATH)) $criteria->add(PagesPeer::FULLPATH, $this->fullpath);
        if ($this->isColumnModified(PagesPeer::PARENT)) $criteria->add(PagesPeer::PARENT, $this->parent);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(PagesPeer::DATABASE_NAME);
        $criteria->add(PagesPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Pages (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTitle($this->getTitle());
        $copyObj->setText($this->getText());
        $copyObj->setAlias($this->getAlias());
        $copyObj->setFullpath($this->getFullpath());
        $copyObj->setParent($this->getParent());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getPagessRelatedById() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPagesRelatedById($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Pages Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return PagesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PagesPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Pages object.
     *
     * @param                  Pages $v
     * @return Pages The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPagesRelatedByParent(Pages $v = null)
    {
        if ($v === null) {
            $this->setParent(NULL);
        } else {
            $this->setParent($v->getId());
        }

        $this->aPagesRelatedByParent = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Pages object, it will not be re-added.
        if ($v !== null) {
            $v->addPagesRelatedById($this);
        }


        return $this;
    }


    /**
     * Get the associated Pages object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Pages The associated Pages object.
     * @throws PropelException
     */
    public function getPagesRelatedByParent(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPagesRelatedByParent === null && ($this->parent !== null) && $doQuery) {
            $this->aPagesRelatedByParent = PagesQuery::create()->findPk($this->parent, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPagesRelatedByParent->addPagessRelatedById($this);
             */
        }

        return $this->aPagesRelatedByParent;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('PagesRelatedById' == $relationName) {
            $this->initPagessRelatedById();
        }
    }

    /**
     * Clears out the collPagessRelatedById collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Pages The current object (for fluent API support)
     * @see        addPagessRelatedById()
     */
    public function clearPagessRelatedById()
    {
        $this->collPagessRelatedById = null; // important to set this to null since that means it is uninitialized
        $this->collPagessRelatedByIdPartial = null;

        return $this;
    }

    /**
     * reset is the collPagessRelatedById collection loaded partially
     *
     * @return void
     */
    public function resetPartialPagessRelatedById($v = true)
    {
        $this->collPagessRelatedByIdPartial = $v;
    }

    /**
     * Initializes the collPagessRelatedById collection.
     *
     * By default this just sets the collPagessRelatedById collection to an empty array (like clearcollPagessRelatedById());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPagessRelatedById($overrideExisting = true)
    {
        if (null !== $this->collPagessRelatedById && !$overrideExisting) {
            return;
        }
        $this->collPagessRelatedById = new PropelObjectCollection();
        $this->collPagessRelatedById->setModel('Pages');
    }

    /**
     * Gets an array of Pages objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Pages is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Pages[] List of Pages objects
     * @throws PropelException
     */
    public function getPagessRelatedById($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPagessRelatedByIdPartial && !$this->isNew();
        if (null === $this->collPagessRelatedById || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPagessRelatedById) {
                // return empty collection
                $this->initPagessRelatedById();
            } else {
                $collPagessRelatedById = PagesQuery::create(null, $criteria)
                    ->filterByPagesRelatedByParent($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPagessRelatedByIdPartial && count($collPagessRelatedById)) {
                      $this->initPagessRelatedById(false);

                      foreach ($collPagessRelatedById as $obj) {
                        if (false == $this->collPagessRelatedById->contains($obj)) {
                          $this->collPagessRelatedById->append($obj);
                        }
                      }

                      $this->collPagessRelatedByIdPartial = true;
                    }

                    $collPagessRelatedById->getInternalIterator()->rewind();

                    return $collPagessRelatedById;
                }

                if ($partial && $this->collPagessRelatedById) {
                    foreach ($this->collPagessRelatedById as $obj) {
                        if ($obj->isNew()) {
                            $collPagessRelatedById[] = $obj;
                        }
                    }
                }

                $this->collPagessRelatedById = $collPagessRelatedById;
                $this->collPagessRelatedByIdPartial = false;
            }
        }

        return $this->collPagessRelatedById;
    }

    /**
     * Sets a collection of PagesRelatedById objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $pagessRelatedById A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Pages The current object (for fluent API support)
     */
    public function setPagessRelatedById(PropelCollection $pagessRelatedById, PropelPDO $con = null)
    {
        $pagessRelatedByIdToDelete = $this->getPagessRelatedById(new Criteria(), $con)->diff($pagessRelatedById);


        $this->pagessRelatedByIdScheduledForDeletion = $pagessRelatedByIdToDelete;

        foreach ($pagessRelatedByIdToDelete as $pagesRelatedByIdRemoved) {
            $pagesRelatedByIdRemoved->setPagesRelatedByParent(null);
        }

        $this->collPagessRelatedById = null;
        foreach ($pagessRelatedById as $pagesRelatedById) {
            $this->addPagesRelatedById($pagesRelatedById);
        }

        $this->collPagessRelatedById = $pagessRelatedById;
        $this->collPagessRelatedByIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Pages objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Pages objects.
     * @throws PropelException
     */
    public function countPagessRelatedById(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPagessRelatedByIdPartial && !$this->isNew();
        if (null === $this->collPagessRelatedById || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPagessRelatedById) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPagessRelatedById());
            }
            $query = PagesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPagesRelatedByParent($this)
                ->count($con);
        }

        return count($this->collPagessRelatedById);
    }

    /**
     * Method called to associate a Pages object to this object
     * through the Pages foreign key attribute.
     *
     * @param    Pages $l Pages
     * @return Pages The current object (for fluent API support)
     */
    public function addPagesRelatedById(Pages $l)
    {
        if ($this->collPagessRelatedById === null) {
            $this->initPagessRelatedById();
            $this->collPagessRelatedByIdPartial = true;
        }

        if (!in_array($l, $this->collPagessRelatedById->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPagesRelatedById($l);

            if ($this->pagessRelatedByIdScheduledForDeletion and $this->pagessRelatedByIdScheduledForDeletion->contains($l)) {
                $this->pagessRelatedByIdScheduledForDeletion->remove($this->pagessRelatedByIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	PagesRelatedById $pagesRelatedById The pagesRelatedById object to add.
     */
    protected function doAddPagesRelatedById($pagesRelatedById)
    {
        $this->collPagessRelatedById[]= $pagesRelatedById;
        $pagesRelatedById->setPagesRelatedByParent($this);
    }

    /**
     * @param	PagesRelatedById $pagesRelatedById The pagesRelatedById object to remove.
     * @return Pages The current object (for fluent API support)
     */
    public function removePagesRelatedById($pagesRelatedById)
    {
        if ($this->getPagessRelatedById()->contains($pagesRelatedById)) {
            $this->collPagessRelatedById->remove($this->collPagessRelatedById->search($pagesRelatedById));
            if (null === $this->pagessRelatedByIdScheduledForDeletion) {
                $this->pagessRelatedByIdScheduledForDeletion = clone $this->collPagessRelatedById;
                $this->pagessRelatedByIdScheduledForDeletion->clear();
            }
            $this->pagessRelatedByIdScheduledForDeletion[]= $pagesRelatedById;
            $pagesRelatedById->setPagesRelatedByParent(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->title = null;
        $this->text = null;
        $this->alias = null;
        $this->fullpath = null;
        $this->parent = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collPagessRelatedById) {
                foreach ($this->collPagessRelatedById as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aPagesRelatedByParent instanceof Persistent) {
              $this->aPagesRelatedByParent->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collPagessRelatedById instanceof PropelCollection) {
            $this->collPagessRelatedById->clearIterator();
        }
        $this->collPagessRelatedById = null;
        $this->aPagesRelatedByParent = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PagesPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}
