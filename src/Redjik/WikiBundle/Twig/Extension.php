<?php
/**
 * @author Ivan Matveev <Redjiks@gmail.com>.
 */

namespace Redjik\WikiBundle\Twig;


use Closure;
use Criteria;
use Redjik\WikiBundle\Model\PagesPeer;
use Redjik\WikiBundle\Model\PagesQuery;

/**
 * Class Extension
 *
 * Twig extension
 * Adds filter for wiki markup parsing
 *
 * @package Redjik\WikiBundle\Twig
 */
class Extension extends \Twig_Extension
{

    const WRONG_LINK_SYMBOL = '!';
    const LINK_PATTERN = '#(\[\[)(.*)\]\]#u';
    const LINK_IDENTIFIER = '[[';

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('wiki', array($this, 'wikiFilter')),
            new \Twig_SimpleFilter('parseLinks', array($this, 'parseLinksFilter')),
        );
    }

    /**
     * Generates html tags for wiki syntax
     * @param $value
     * @return mixed
     */
    public function wikiFilter($value)
    {
        $value = nl2br(strip_tags($value));
        return preg_replace_callback($this->getParsers(),[$this,'callback'],$value);
    }

    /**
     * Checks Pages for valid links
     * Replaces link pattern with unfound link pattern
     * @param string $value
     * @return string mixed
     */
    public function parseLinksFilter($value)
    {
        $links = $this->getRelativeLinksFromText($value);
        $criteria = new Criteria();
        $criteria->add(PagesPeer::FULLPATH,$links,Criteria::IN);
        $pages = PagesQuery::create(null, $criteria)->select([PagesPeer::FULLPATH])->find()->toArray();
        $linksToMark = array_diff($links,$pages);
        array_walk($linksToMark,function(&$value){$value = '#'.$value.'#u';});
        return preg_replace($linksToMark,self::WRONG_LINK_SYMBOL.'$0',$value);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'redjik_wiki_twig_extension';
    }

    /**
     * Regexes with callbacks
     * @return array
     */
    protected function getParsers()
    {
        return [
            '#(//)(.*)//#u',
            '#(\*\*)(.*)\*\*#u',
            '#(__)(.*)__#u',
            '#(")(.*)"#u',
            self::LINK_PATTERN

        ];
    }

    /**
     * Gets relative links form text, removes trailing slashes from text
     * @param $value
     * @return array
     */
    protected function getRelativeLinksFromText($value)
    {
        preg_match_all(self::LINK_PATTERN,$value,$matches);
        $linksToCheck = [];
        if (isset($matches[2])){
            foreach ($matches[2] as $linkString)
            {
                $linkStringPieces = explode(' ',$linkString);
                $link = $linkStringPieces[0];
                $url = parse_url($link);
                if (isset($url['scheme'])){
                    continue;
                }

                //remove trailing slash
                if (mb_strpos($link,'.',null,'utf8')===0){
                    $link = mb_substr($link,1,null,'utf8');
                }

                $linksToCheck[] = $link;
            }
        }
        return $linksToCheck;
    }


    /**
     * @return Closure[]
     */
    protected function getMatchCallback()
    {
        return [
            '**'=>function($value){return '<b>'.$value.'</b>';},
            '//'=>function($value){return '<i>'.$value.'</i>';},
            '__'=>function($value){return '<u>'.$value.'</u>';},
            '"'=>function($value){return '&laquo;'.$value.'&raquo;';},
            self::LINK_IDENTIFIER=>function($value){
                    $badLink = '';
                    if (mb_strpos($value,self::WRONG_LINK_SYMBOL,null,'utf8')===0){
                        $badLink = 'style="color:red"';
                        $value = mb_substr($value,1,null,'utf8');
                    }

                    $linksParts = explode(' ',$value);
                    $href = $linksParts[0];
                    if (count($linksParts) > 1){
                        array_shift($linksParts);
                        $name = implode(' ',$linksParts);
                    }else{
                        $name = $linksParts[0];
                    }

                    if ($badLink){
                        $href.='/add';
                    }

                    return '<a '.$badLink.' href="'.$href.'">'.$name.'</a>';
                },
        ];
    }

    /**
     * Fire callback
     * @param $matches
     * @return bool
     */
    protected function callback($matches)
    {
        $callback = $this->getMatchCallback();
        if (isset($callback[$matches[1]])){
            return $callback[$matches[1]]($matches[2]);
        }

        return false;
    }



} 