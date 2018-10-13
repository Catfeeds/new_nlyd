<header class="mui-bar mui-bar-nav">
    <a class="mui-pull-left nl-goback">
        <i class="iconfont">&#xe610;</i>
    </a>
    <h1 class="mui-title"><?=__('比赛规则', 'nlyd-student')?></h1>
</header>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <!--<div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <div class="layui-row nl-border nl-content">
                <div class="width-margin width-margin-pc">
                    <div class="rule-row">
                        <div class="rule-number">1</div>
                        <div class="rule-title">
                            <div class="rule-name c_blue">比赛总则</div>
                            <div class="dot">
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                            </div>
                        </div>

                        <div class="rule-content">
                            <p class="rule-item">1、每个比赛项目共三轮，每轮设置相应间隔时间。</p>
                            <p class="rule-item">2、选手成绩按分数排名，分数相同按比赛时间排名，用时少排名优先，分数及排名相同按正确率排名，三项排名标准相同则并次排名，每个项目计分规则请查看项目详情。</p>
                        </div>
                    </div>

                    <div class="rule-row">
                        <div class="rule-number">2</div>
                        <div class="rule-title">
                            <div class="rule-name c_blue">比赛流程</div>
                            <div class="dot">
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                            </div>
                        </div>

                        <div class="rule-content">
                            <p class="rule-item">比赛共6个项目，每个项目比赛3轮，每个项目及每轮比赛由比赛平台设置相应间隔时间。</p>
                        </div>
                    </div>

                    <div class="rule-row">
                        <div class="rule-number">3</div>
                        <div class="rule-title">
                            <div class="rule-name c_blue">比赛成绩</div>
                            <div class="dot">
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                            </div>
                        </div>

                        <div class="rule-content">
                            <p class="rule-item">比赛选手答题结束后，可查看得分情况（答题数，分数，用时，正确率）全部选手提交后，可查看详细排名。</p>
                        </div>
                    </div>

                    <div class="rule-row">
                        <div class="rule-number">4</div>
                        <div class="rule-title">
                            <div class="rule-name c_blue">比赛比赛细则</div>
                            <div class="dot">
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                                <div class="t"></div>
                                <div class="w"></div>
                            </div>
                        </div>

                        <div class="rule-content">
                            <p class="rule-item">1、<span class="c_blue">数字争霸</span>即在比赛时系统给出100位随即数字，选手记忆后按正确的顺序复位数字。</p>
                            <p class="rule-item">2、正确复位1位数字<span class="c_blue">+12分</span>，若选手所复位数字全部正确，剩余1秒多<span class="c_blue">+1分</span>，若复位数字有错误，则时间分不计入成绩。</p>
                        </div>

                        <div class="rule-content">
                            <p class="rule-item">1、<span class="c_blue">扑克接力</span>即在比赛时系统给出52张扑克牌随机数字，选手记忆后按正确的顺序复位扑克牌面。</p>
                            <p class="rule-item">2、正确复位1张牌<span class="c_blue">+18分</span>，若选手所复位扑克牌全部正确，剩余1秒多<span class="c_blue">+1分</span>，若复位扑克牌有错误，则时间分不计入成绩。</p>
                        </div>

                        <div class="rule-content">
                            <p class="rule-item">1、<span class="c_blue">快眼扫描</span>即在比赛时系统快速闪现一组字符0.8秒的时间，选手在5秒内从选项中选择正确答案，答案即判断是否正确，答对一题<span class="c_blue">+10分</span>。</p>
                            <p class="rule-item">2、在比赛时间内先错误10题，选手本轮比赛即结束，本轮比赛<span class="c_blue">无时间分</span></p>
                        </div>

                        <div class="rule-content">
                            <p class="rule-item">1、<span class="c_blue">文章速读</span>即在比赛时选手快速阅读一篇2000字左右的文章，阅读完成，根据记忆信息回答10道选择题，本项目在比赛结束前可返回修改答案。</p>
                            <p class="rule-item">2、正确一题<span class="c_blue">+23分</span>，若选手所回答题目全部正确，剩余1秒多<span class="c_blue">+1分</span>，若复位数字有错误，则时间分不计入成绩。</p>
                        </div>

                        <div class="rule-content">
                            <p class="rule-item">1、<span class="c_blue">正向速算</span>即在比赛时选手依次进行“连加运算” “加减混合运算” “乘除运算”三类题型的比赛,每类题型3分钟,答对一题<span class="c_blue">+10分</span>，每道题选手给出确认答案后选择下一题，即刻显示答案是否正确。</p>
                            <p class="rule-item">2、答题数量多且正确数多，则分数越高，未给出答题直接下一题，视为未作答，本项目<span class="c_blue">无时间分</span>。</p>
                        </div>

                        <div class="rule-content">
                            <p class="rule-item">1、<span class="c_blue">逆向运算</span>即在比赛中，选手运用给出的运算符号，用完给出一组（4个）字符，使其运算结果等于24。</p>
                            <p class="rule-item">2、跳过不作答直接扣掉两秒时间，在规定时间内，答对题数越多，分数越高。</p>
                        </div>
                    </div>
                </div>
            </div>           
        </div>-->
      <?=$post_content?>

    </div>
</div>

<script>
jQuery(function($) { 

})
</script>