<?php
class Pagination{
    /**
     * 每頁顯示的記錄數
     *
     * @var Int
     */
    private $disp_result_number;
    /**
     * 總頁數
     *
     * @var Int
     */
    private $pagesNum;
    /**
     * 需要顯示多少個分頁Item,默認為5
     *
     * @var Int
     */
    private $dispPagesNum;
    /**
     * 當前頁號
     *
     * @var Int
     */
    private $currentPage;
    /**
     * 分頁的顯示樣式
     *
     * @var Array
     */
    /*public $disp_style=array(
        'front'   => ' <a href="{url}">&lt;&lt;front</a> ',             //前一頁
        'first'   => ' <a href="{url}"><b>{page}</b></a> ',             //第一頁
        'item'    => ' <a href="{url}">{page}</a> ',                    //每一頁
        'current' => ' <span style="color:red">[{page}]</span> ',         //當前頁
        'more'    => ' .. ',                                              //更多的時候顯示的字符
        'last'    => ' <a href="{url}"><b>{page}</b></a> ',             //最后一頁
        'next'    => ' <a href="{url}">next&gt;&gt;</a> ',              //下一頁
    );*/
//    public $disp_style=array(
//        'front'   => ' <li><a href="{url}" aria-label="Previous"><span aria-hidden="true">&laquo;front</span></a></li> ',             //前一頁
//        'first'   => ' <li><a href="{url}">{page}</a></li> ',             //第一頁
//        'item'    => ' <li><a href="{url}">{page}</a></li> ',                    //每一頁
//        'current' => ' <li class="active"><a href="#">{page}</a></li> ',         //當前頁
//        'more'    => ' <li class="disabled"><a href="javascript:;">..</a></li> ',  //更多的時候顯示的字符
//        'last'    => ' <li><a href="{url}">{page}</a></li> ',             //最后一頁
//        'next'    => ' <li><a href="{url}"><span aria-hidden="true">next&raquo;</span></a></li> ',              //下一頁
//    );
    public $disp_style=array(
        'front'   => '<li class="disabled"><a class="arrow iconfont icon-double-arrow-l" href="{url}"></a></li>',             //前一頁
        'first'   => '',             //第一頁
        'item'    => '<li><a href="{url}">{page}</a></li>',                    //每一頁
        'current' => '<li class="active"><span>{page}</span></li>',         //當前頁
        'more'    => '',  //更多的時候顯示的字符
        'last'    => '',             //最后一頁
        'next'    => '<li><a class="arrow iconfont icon-double-arrow-r" href="{url}"></a></li>',              //下一頁
    );
    /**
     * 总记录数
     *
     * @var Int
     */
    private $total_num;
    /**
     * URL链接
     *
     * @var string
     */
    private $url;

	/**
	 * 獲取頁面字符串的囘調函數
	 */
	private $page_callback_func = null;

    /**
     * 构造函数,给定:总记录数,当前页数,每页显示的记录数,分页样式0-5,是否绍终显示第一页和最后一页
     * @param int $resultNumber 总记录数
     * @param int $current 当前页数
     * @param int $rowNum 每页显示的记录数
     * @param int $dispNum 活动页数
     */
    public function __construct($resultNumber,$current,$rowNum,$dispNum=5){
        if ($current<1) $current  = 1;
        $this->pagesNum           = ceil($resultNumber/$rowNum);  //总页数
        if ($current > $this->pagesNum)
            $current = $this->pagesNum;
        $this->currentPage        = $current;                     //当前页数
        $this->disp_result_number = $rowNum;                      //每页显示的记录数
        $this->dispPagesNum       = $dispNum;                     //显示的活动页数
        $this->total_num          = $resultNumber;                  //总页数
    }

	/**
	 * 獲取頁面字符串的囘調函數
     * 回调函数需要接受二个参数，第一个为模板字符串，第二个为要处理的页码。函数需要返回一个新的字符串
     * 比如 
     * function myPageItem($template_string, $page_number){
     *      return replace('/{page}/', '/'. ($page_number * 100) . '/', $template_string);
     * }
	 */
	public function setCallback($function){
		$this->page_callback_func = $function;
	}

    /**
     * 设置自己的样式
     * @param String $key 分頁樣式:front\first\item\current\more\last\next
     * @param String $value 值，可以用於替換的{url}為URL \ {page}為分頁數
     */
    public function setStyle($key, $value) {
        $this->disp_style [$key] = $value;
    }
    /**
     * 設置用於替換模板裡面的{url}值,$url裡面允許使用{page}來替換page變量
     */
    public function setUrl($url){
        $this->url = $url;
    }
    /**
     * 獲得一個Item的顯示字符串
     *
     * @param Int $page
     * @param String $tmp templage
     * @return String
     */
    public function getPageStr($page, $tmp = null) {

        if($this->pagesNum == 0) $page = 1;
        elseif ($page > $this->pagesNum) $page = $this->pagesNum;
        elseif ($page < 1) $page = 1;

        if(!$tmp){
            if ($page==$this->currentPage && isset($this->disp_style['current']))
            {
                $template = $this->disp_style['current'];
            }
            elseif ($page==1 && isset($this->disp_style['first']))
            {
                $template = $this->disp_style['first'];
            }
            elseif ($page==$this->pagesNum && isset($this->disp_style['last']))
            {
                $template = $this->disp_style['last'];
            }
            else
            {
                $template = $this->disp_style['item'];
            }
        }else{
            $template = $tmp;
        }

        $str = str_replace('{url}' , $this->url, $template);
		if($this->page_callback_func){
			$str = call_user_func_array($this->page_callback_func,array($str,$page));
		}else{
			$str = str_replace('{page}', $page,      $str);
		}

        return $str;
    }
    /**
     * 输出内容
     * @return string
     */
    public function getOutput(){

        $middle   = floor($this->dispPagesNum/2);               //中间数
        $startRow = 1;                                          //开始下标
        $endRow   = $this->pagesNum;                            //结束下标

        // 如果最大就1页就不显示
        if ($endRow <= 1) {
            return '';
        }

        if($this->pagesNum>$this->dispPagesNum){
            if($this->currentPage>$middle+1 && $this->currentPage+$middle<$this->pagesNum){ //中間
                $startRow = $this->currentPage - $middle;
                $endRow   = $this->currentPage + $middle;
            }else{
                if($this->currentPage>$middle+1){ //後端超出
                    //echo "後端超出";
                    $startRow = ($this->pagesNum-$this->dispPagesNum>1) ? ($this->pagesNum-$this->dispPagesNum+1) : 1;
                    $endRow   = $this->pagesNum;
                }else{ //前端超出
                    //echo "前端超出";
                    $startRow = 1;
                    $endRow   = (1+$this->dispPagesNum<$this->pagesNum) ? $this->dispPagesNum : $this->pagesNum;
                }
            }
        }

        $out='';                                                //用于输出的字串
        for ($i=$startRow;$i<$endRow+1;$i++){
            if ($this->currentPage==$i && isset($this->disp_style['current'])){
                $template = 'current';
            }else {
                $template = 'item';
            }
            $out.= $this->_replace($template,$i);
        }
        $previousPage=$this->currentPage-1;                     //上一页
        $nextPage=$this->currentPage+1;                         //下一页
        $more = isset($this->disp_style['more']) ? $this->disp_style['more'] : '';

        $previous = $startRow > 1;
        $next     = $endRow   < $this->pagesNum;

        if ($previous){                                         //如果有前导,加上前导
            $str = '';
            if(isset($this->disp_style['front'])){
                $str.= $this->_replace('front',$previousPage);
            }
            if (isset($this->disp_style['first'])){             //是否显示最前页
                $str.= $this->_replace('first',1);
            }
            $out = $str.$more.$out;
        }
        if ($next){                                             //如果有后导,加上后导
            $str = '';
            if (isset($this->disp_style['last'])){              //是否显示最后页
                $str.= $this->_replace('last',$this->pagesNum);
            }
            if(isset($this->disp_style['next'])){
                $str.= $this->_replace('next',$nextPage);
            }
            $out.= $more.$str;
        }
        return $out;
    }
    private function _replace($style,$page){
        $str = str_replace(['{url}','{page}'], [$this->url,$page], $this->disp_style[$style]);
		if($this->page_callback_func){
			return call_user_func_array($this->page_callback_func,array($str,$page));
		}
        return $str;
    }
    /**
     * 获得总页数
     * @return int
     */
    public function getCountPages(){
        return $this->pagesNum;
    }
    /**
     * 获得总记录数
     * @return int
    */
    public function getCountResult(){
        return $this->total_num;
    }

    /**
     *
     * @return array
     */
    public function getAryResult(){
        $ary['page_n']= $this->pagesNum;
        $ary['start_n']=$this->getCurrentPageStart();
        $ary['end_n']=$this->getCurrentPageEnd();
        $ary['all_n']= $this->total_num;
        $ary['now']= $this->getCurrentPageNumber();

        return $ary;
    }

    /**
     * 获得当前页的开始记录数
     * @return int
     */
    public function getCurrentPageStart(){
        $i = $this->currentPage * $this->disp_result_number - $this->disp_result_number;
        return $i+1;
    }
    /**
     * 获得当前页的结束记录数
     * @return int
     */
    public function getCurrentPageEnd(){
        $i = $this->currentPage * $this->disp_result_number;
        return ($i > $this->total_num? $this->total_num : $i);
    }
    /**
     * 获得当前页的页号
     * @return int
     */
    public function getCurrentPageNumber(){
        return $this->currentPage;
    }

    public function simplePager($params) {
        /*$p->setStyle('more','');
        $p->setStyle('first','');
        $p->setStyle('last','');*/
        $this->setStyle ( 'front', '<a href="{url}">上一頁</a>|' );
        $this->setStyle ( 'next', '|<a href="{url}">下一頁</a>' );
        $this->setUrl ( $params );
        return $this->getOutput ();
    }
}

if(false){
    function o($s){echo "\n<br />",$s,"\n<br />";}
	$list = array(00,11,22,33,44,55,66,77,88,99);
	unset($list[0]);
	$f = function($s,$p){
		global $list;
		echo " $s => $p \n\n";
		return preg_replace('/{page}/ie','sprintf($list[$p])',$s);
	};
	$page = empty($_GET['p']) ? 7 : $_GET['p'];
    $p = new Pagination(count($list),$page,1,3);
	$p->setCallback($f);
    $p->setUrl('?p={page}');
#    o('总页数:'.$p->getCountPages());        //总页数
#    o('总记录数:'.$p->getCountResult());       //总记录数
#    o('当前页的开始:'.$p->getCurrentPageStart());  //当前页的开始
#    o('当前页的结束:'.$p->getCurrentPageEnd());    //当前页的结束
#    o('当前页号:'.$p->getCurrentPageNumber()); //当前页号
#    o('获得一页的样式:'.$p->getPageStr(4));         //获得一页的样式
#    $p->setStyle('more','..');
#    $p->setStyle('first','<a href="?p=1">1</a> <a href="?p=2">2</a>');
#    $p->setStyle('last','<a href="?p={page*10}">{page-1}</a> <a href="{url}">{page/10} & {page*10} </a>');
#    $p->setStyle('front','<a href="{url}">前一頁</a>|');
#    $p->setStyle('next','|<a href="{url}">后一頁</a>');
    echo $p->getOutput();
}

