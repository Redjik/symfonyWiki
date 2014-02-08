<?php

namespace Redjik\WikiBundle\Model;

use Redjik\WikiBundle\Model\om\BasePages;

class Pages extends BasePages
{

    /**
     * Saves page, modifies properties
     * @param Pages|null $parentPage
     * @return bool
     */
    public function savePage($parentPage=null)
    {
        $this->setDefaultProperties($parentPage);
        if ($this->validate()){
            $propel = \Propel::getConnection();
            $propel->beginTransaction();
            try{
                if ($this->save()){
                    $this->generateFullpathForChildren();
                    $propel->commit();
                    return true;
                }else{
                    $propel->rollBack();
                }
            }catch (\PropelException $e){
                $propel->rollBack();
            }
        }

        return false;
    }

    /**
     * @param Pages|null $parentPage
     */
    protected function setDefaultProperties($parentPage = null)
    {
        if ($parentPage){
            $this->setParent($parentPage->getId());
        }else{
            $this->setParent(null);
        }

        if ($this->getAlias() === ''){
            $this->setAlias($this->traslitirate($this->getTitle()));
        }else{
            $this->setAlias($this->traslitirate($this->getAlias()));
        }

        $this->setPathFromParent($parentPage);
    }

    /**
     * Should be in transaction
     * Recursion - care.
     * @TODO should be in trigger in DB
     * @TODO or redo to Nested sets for the sake of one query
     * @return void
     */
    public function generateFullpathForChildren()
    {
        if ((!$this->isNew()) || $this->isColumnModified(PagesPeer::FULLPATH)){
            foreach ($this->getPagessRelatedById() as $page)
            {
                $page->setPathFromParent($this);
                $page->generateFullpathForChildren();
                $page->save();
            }
        }
    }

    /**
     * Sets full path concatenating with parent fullpath
     * @param Pages|null $parent
     */
    protected function setPathFromParent($parent = null)
    {
        if ($parent === null){
            $this->setFullpath($this->getAlias());
        }else{
            $this->setFullpath($parent->getFullpath().'/'.$this->getAlias());
        }
    }

    /**
     * @param string $text
     * @return string translitirated value
     */
    protected function traslitirate($text)
    {
        $trans = array("а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "yo", "ж" => "zh", "з" => "z", "и" => "i",
                       "й" => "i", "к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "y",
                       "ф" => "f", "х" => "h", "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "sh", "ы" => "i", "э" => "e", "ю" => "u", "я" => "ya",
                       "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D", "Е" => "E", "Ё" => "Yo", "Ж" => "ZH", "З" => "Z", "И" => "I", "Й" => "I",
                       "К" => "K", "Л" => "L", "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T", "У" => "Y", "Ф" => "F",
                       "Х" => "H", "Ц" => "C", "Ч" => "Ch", "Ш" => "Sh", "Щ" => "Sh", "Ы" => "I", "Э" => "E", "Ю" => "U", "Я" => "Ya", "ь" => "", "Ь" => "",
                       "ъ" => "", "Ъ" => "", ' ' => '_');
        $text = mb_strtolower(strtr($text, $trans), 'utf8');
        $text = preg_replace('#[^a-z0-9_]#', '_', $text);
        $text = preg_replace('#_+#', '_', $text);
        $text = preg_replace('#_$#', '', $text);
        return $text;
    }
}
