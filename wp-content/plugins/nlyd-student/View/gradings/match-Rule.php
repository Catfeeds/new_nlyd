<style>
.ready_img{
    width:30px;
    display:inline-block;
    vertical-align: middle;
}
.title_name{
    display:inline-block;
    vertical-align: middle;  
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
        require_once leo_student_public_view.'leftMenu.php';
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12  detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                    
                </a>
                <h1 class="mui-title">
                    <div>
                        <?=__('记忆项目考级规程', 'nlyd-student')?>
                    </div>

                </h1>
            </header>
            <pre class="width-margin width-margin-pc c_black ff_cn">
            <?php
            switch ($_GET['type']){
                case 'jy':
        ?>

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <div class="title_name"><?=__('考级规程', 'nlyd-student')?></div></div>
　　国际记忆水平测试（International Memory Ability Test，简称IMAT），是国际脑力运动委员会（IISC）用于考察受试者记忆水平的国际性评价系统。该系统在传统“记忆大师”评价标准的基础上，删除了繁冗的重复测试项，增设了长期记忆能力测试，弥补了传统测评的不足，让记忆水平级别判定更为科学。其中长期记忆水平测试内容根据各国文化传统分别设定。
　　测试分为短期记忆和长期记忆两方面记忆能力。
　　短期记忆主要考察受试者短期看记和听记随机信息的能力，纵向考察抽象信息快速形象化处理能力，横向考察短期记忆容量的大小。短期记忆信息以词汇、数字、英文字母、人脉资料（人名、头像、电话号码）等随机信息为测试内容，受试者在规定时间内完成各级指定内容的记忆。
　　长期记忆主要考察受试者长期记忆信息和大容量记忆信息的能力，国际记忆水平测试（中国）体系中以圆周率常数和国学经典原文为测试内容，受试者按要求默写各级指定内容。其中国学经典内容，根据中国教育部颁布的《完善中华优秀传统文化教育指导纲要》，收录了弘扬中华优秀传统文化必读的12本国学经典正版原文，包括4本蒙学经典《弟子规》《千字文》《声律启蒙》《四字鉴略》，5本思想经典《大学》《中庸》《道德经》《孙子兵法》《论语》，3本诗词经典《唐诗三百首》《宋词三百首》《诗经》，涵盖了语言文字、百科、文学、思想、历史等多个领域，旨在让参训者和受试者在记忆技能和国学素养两方面同时进步，品学兼修。

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div>  <div class="title_name"><?=__('分级标准', 'nlyd-student')?></div></div>
　  各级测试要求短期记忆测试全部达标，一级和二级的长期记忆全部正确，其它各级的长期记忆测试总错字（包括错别字和空白）不超过10个。

<div class="bold ta_c c_black fs_16"><div class="title_name"><?=__('(一)初等记忆水平', 'nlyd-student')?></div></div>
<span class="c_blue">记忆一级：</span>
<span class="c_black bold">1、短期记忆:</span>
    （1）5分钟记忆随机数字30位。
    （2）15分钟记忆随机中文词语30个。
<span class="c_black bold">2、长期记忆:</span>
    （1）按顺序默写出圆周率前100位数字。

<span class="c_blue">记忆二级：</span>
<span class="c_black bold">1、短期记忆:</span>
    （1）5分钟记忆随机数字40位。
    （2）15分钟记忆随机中文词语40个。
<span class="c_black bold">2、长期记忆:</span>
    （1）按顺序默写出圆周率前200位数字。

<span class="c_blue">记忆三级：</span>
<span class="c_black bold">1、短期记忆:</span>
    （1）5分钟记忆随机数字60位。
    （2）15分钟记忆随机中文词语50个。
    （3）5分钟记忆随机英文字母30个。
<span class="c_black bold">2、长期记忆:</span>
    （1）从《弟子规》（约1080字）《千字文》（约1000字）全文中随机截取3段默写，每段100字。

<div class="bold ta_c c_black fs_16"><div class="title_name"><?=__('(二)中等记忆水平', 'nlyd-student')?></div></div>
<span class="c_blue">记忆四级：</span>
<span class="c_black bold">1、短期记忆:</span>
    （1）5分钟记忆随机数字80位。
    （2）15分钟记忆随机中文词语60个。
    （3）5分钟记忆随机英文字母40个。
    （4）听记数字40个（中文语音，1个/秒）。
<span class="c_black bold">2、长期记忆:</span>
    （1）本级新增：从《唐诗三百首》（约20000字）全文中随机截取3段默写，每段100字。
    （2）下级内容：从《弟子规》《千字文》全文中随机截取3段默写，每段100字。

<span class="c_blue">记忆五级：</span>
<span class="c_black bold">1、短期记忆:</span>
    （1）5分钟记忆随机数字120位。
    （2）15分钟记忆随机中文词语80个。
    （3）5分钟记忆随机英文字母50个。
    （4）听记数字45个（中文语音，1个/秒）。
<span class="c_black bold">2、长期记忆:</span>
    （1）本级新增：从《声律启蒙》（约6900字）《大学》（约1889字）全文中随机截取3段默写，每段100字。
    （2）下级内容：从《弟子规》《千字文》《唐诗三百首》全文中随机截取3段默写，每段100字。

<span class="c_blue">记忆六级：</span>
<span class="c_black bold">1、短期记忆:</span>
    （1）5分钟记忆随机数字160位。
    （2）15分钟记忆随机中文词语100个。
    （3）5分钟记忆随机英文字母60个。
    （4）听记数字50个（中文语音，1个/秒）。
<span class="c_black bold">2、长期记忆:</span>
    （1）本级新增：从《中庸》（约3568字）《道德经》（约5284字）全文中随机截取3段默写，每段100字。
    （2）下级内容：从《弟子规》《千字文》《唐诗三百首》《声律启蒙》《大学》全文中随机截取3段默写，每段100字。

<span class="c_blue">记忆七级：</span>
<span class="c_black bold">1、短期记忆:</span>
    （1）5分钟记忆随机数字200位。
    （2）15分钟记忆随机中文词语120个。
    （3）听记数字60个（中文语音，1个/秒）。
    （4）10分钟记忆5组人脉信息（头像、人名、电话）
<span class="c_black bold">2、长期记忆:</span>
    （1）本级新增：从《四字鉴略》（约4150字）《孙子兵法》（约6111字）全文中随机截取3段默写，每段100字。
    （2）下级内容：从《弟子规》《千字文》《唐诗三百首》《声律启蒙》《大学》《中庸》《道德经》全文中随机截取3段默写，每段100字。

<div class="bold ta_c c_black fs_16"><div class="title_name"><?=__('(三)高等记忆水平', 'nlyd-student')?></div></div>
<span class="c_blue">记忆八级：</span>
<span class="c_black bold">1、短期记忆:</span>
    （1）5分钟记忆随机数字240位。
    （2）15分钟记忆随机中文词语140个。
    （3）听记数字70个（中文语音，1个/秒）。
    （4）10分钟记忆6组人脉信息（头像、人名、电话）。
<span class="c_black bold">2、长期记忆:</span>
    （1）本级新增：从《宋词三百首》全文（约55754字）中随机截取3段默写，每段100字。
    （2）下级内容：从《弟子规》《千字文》《唐诗三百首》《声律启蒙》《大学》《中庸》《道德经》《四字鉴略》《孙子兵法》全文中随机截取3段默写，每段100字。

<span class="c_blue">记忆九级：</span>
<span class="c_black bold">1、短期记忆:</span>
    （1）5分钟记忆随机数字280位。
    （2）15分钟记忆随机中文词语160个。
    （3）听记数字80个（中文语音，1个/秒）。
    （4）10分钟记忆8组人脉信息（头像、人名、电话）。
<span class="c_black bold">2、长期记忆:</span>
    （1）本级新增：从《论语》全文（约11705字）中随机截取3段默写，每段100字。
    （2）下级内容：从《弟子规》《千字文》《唐诗三百首》《声律启蒙》《大学》《中庸》《道德经》《四字鉴略》《孙子兵法》《宋词三百首》全文中随机截取3段默写，每段100字。

<span class="c_blue">记忆十级：</span>
<span class="c_black bold">1、短期记忆:</span>
    （1）5分钟记忆随机数字320位。
    （2）15分钟记忆随机中文词语180个。
    （3）听记数字100个（中文语音，1个/秒）。
    （4）10分钟记忆10组人脉信息（头像、人名、电话）。
<span class="c_black bold">2、长期记忆:</span>
    （1）本级新增：从《诗经》全文（约39234字）中随机截取3段默写，每段100字。
    （2）下级内容：从《弟子规》《千字文》《唐诗三百首》《声律启蒙》《大学》《中庸》《道德经》《四字鉴略》《孙子兵法》《宋词三百首》《论语》全文中随机截取3段默写，每段100字。
                <?php
                        break;
                case 'sd': ?>
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <div class="title_name"><?=__('考级规程', 'nlyd-student')?></div></div>
    国际速读水平测试（International Reading Ability Test，简称IRAT）是国际脑力运动委员会在总结多个国际版本的评级标准的基础上，加入更为科学的测评理念，摒除繁杂的计算方式和级别划分方式，从更公平合理、更易于操作、群众更易于接受和理解等多方面综合考虑，制定出了利于高效阅读运动全面普及的水平测试标准。
    受试者以最快速度阅读一篇文章，紧接着就该文章完成10道选择题，以考察理解率（答题正确率即为理解率）。系统自动统计阅读速度和理解率。
    为了规避受试者之前已阅读过测试文章的极端情况，测试时受试者须逐轮阅读3篇文章，以阅读速度居中的那轮水平为最终成绩。

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div>  <div class="title_name"><?=__('分级标准', 'nlyd-student')?></div></div>
　  速读零级：理解率在70%以下；理解率达70%但阅读速度在1000字/分钟以下。
    以下各级评级的<span class="c_orange">前提是理解率达到70%（含）以上：</span>
    速读一级：每分钟1000字及以上。
    速读二级：每分钟2000字及以上。
    速读三级：每分钟3000字及以上。
    ……
    以此类推，每级按1000字/分钟递增，水平越高则级数越大。
                <?php
                    break;
                case 'xs': ?>
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <div class="title_name"><?=__('考级规程', 'nlyd-student')?></div></div>
　  国际心算水平测试（International Arithmetic Ability Test，简称IAAT）是国际脑力运动委员会为了全面普及快速心算运动而制定的水平测试标准。
    民间传统心算水平评级标准较多，但都限于正向速算，而且评级计算方式繁杂，限制了心算运动的推广和普及。为了让国际心算运动更具科学性、群众性和趣味性，国际脑力运动委员会从更公平合理、简便易行、群众更易于接受和理解等多方面，通过正向速算和逆向速算综合考察受试者的逻辑思维水平和数理空间思维能力。
    受试者须测试正向速算和逆向速算两部分内容。系统由易到难随机出题，受试者通过心算直接填写答案。
    正向速算进行9分钟心算，包含连加运算、加减混合运算、乘除运算，各3分钟。加减运算每题最多5个加减数，每个加减数最多4位数，系统随机匹配；乘除运算中的除数及各题中的1个乘数为1位数，被除数和其他乘数最多4位数，系统随机匹配，除不尽时四舍五入取整数。
    逆向速算进行10分钟心算，系统逐一随机出题，每题给出1～13之中的4个数据，选手使用加、减、乘、除运算符号和括号把它们连成一个算式，使其运算结果等于24。
    测试时间内，受试者尽量多地答题，答对1题得10分。其中<span class="c_orange">逆向速算跳过不答题</span>则减少剩余答题时间2秒。

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div>  <div class="title_name"><?=__('分级标准', 'nlyd-student')?></div></div>
    受试者完成全部正向速算和逆向速算测试后，根据各项得分之和判定相应心算水平级别。
    心算一级：400分及以上。
    心算二级：600分及以上。
    心算三级：800分及以上。
    ……
    以此类推，每级按200分递增，水平越高则级数越大。
                <?php
                    break;
                default:
                ?>

        <?php } ?>
            </pre>
        </div>
    </div>
</div>
