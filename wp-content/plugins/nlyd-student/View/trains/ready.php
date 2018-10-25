<style>
.ready_img{
    position: relative;
    width:30px;
    display:inline-block;
    top: 8px;
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
                        <?=__($project_title, 'nlyd-student')?>
                        <?=__($genre_title, 'nlyd-student')?>
                    </div>

                </h1>
            </header>
            <pre class="width-margin width-margin-pc c_black ff_cn">
            <?php
            switch ($_GET['type']){
                case 'szzb':
        ?>
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <?=__('项目意义', 'nlyd-student')?></div>
　　数字是世界公认最难记忆的信息，但运用记忆术可以轻松克服这一难题，同时对大脑的注意力、记忆力、创造力和敏锐度也是一个有效的训练。本赛事向广大群众提供公益性的记忆术培训，掌握技术方法之后利用本训练平台进行自我训练，记忆水平将大幅提高。
　　Numbers are universally acknowledged to be the most difficult information to remember, but memory is an easy way to overcome this problem and an effective training for the brain's attention, memory, creativity and sharpness. This competition provides public welfare memory training to the masses. After mastering the techniques and methods, the training platform will be used for self-training, and the memory level will be greatly improved.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div> <?=__('比赛规程', 'nlyd-student')?></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2. All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。每轮比赛选手在20分钟内完成100个随机数字的记忆和复位，正确率越高、速度越快，得分越高。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds. In each round, 100 random numbers are remembered and reset in 20 minutes. The higher the correct rate, the faster the speed, the higher the score.
　　4、待所有选手答题结束后，系统自动统计并公布本项目所有选手和战队的成绩。
　　4. After the end of this discipline, the system automatically calculates and releases the result.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div>  <?=__('评判标准', 'nlyd-student')?></div>
<span class="c_blue">获胜依据：</span>
<span class="c_blue">Scoring rules:</span>
　　1.数量分：正确记忆1个数字得12分；
　　1. Quantity points: Correctly memorizing 1 digit for 12 points;
　　2.时间分：在20分钟内复位提交后剩余1秒得1分。
　　2. Time points: 1 points for the remaining 1 second after the submission in 20 minutes.
　　3.总分高者胜，总分相同则用时少者胜。
　　3. A high score wins, if the total score is the same, less time wins.

<span class="c_blue">选手成绩：</span>
<span class="c_blue">Personal score:</span>
　　1.当选手记忆数字有错误时，则选手得分=数量分。
　　1. When athlete’s answer is NOT completely right, the score is equal to the quantity points.
　　2.当选手记忆数字全部正确时，则选手得分=数量分+时间分。
　　2. When athlete’s answer is completely right, the score is equal to the quantity points plus time points.
　　3.取最高分轮次的成绩参与排名。
　　3. The highest round score is the final score.

<span class="c_blue">战队成绩：</span>
<span class="c_blue">Team score:</span>
　　以战队各轮前五名选手总分之和为战队得分。
　　The total score of top 5 athletes from each team is the final team score.


                <?php
                        break;
                case 'pkjl': ?>
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <?=__('项目意义', 'nlyd-student')?></div>
　　扑克牌是训练多元素信息记忆能力的极佳工具，训练者要在尽量短的时间内记住一副牌的颜色、图案、字符、顺序等多项信息，各元素的搭配要准确无误，是训练注意力、视觉感知力、记忆力和创造力（想象力）的重要方式。
　　Poker is an excellent tool for training multi-element information memory ability. Trainers should remember as soon as possible the color, pattern, character, sequence and other information of a deck of cards. The combination of various elements must be accurate. It is an important way to train attention, visual perception, memory and creativity (imagination).

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div>  <?=__('比赛规程', 'nlyd-student')?></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2. All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。每轮比赛选手在15分钟内完成1副扑克牌（不含大小王共52张）记忆和复牌，正确率越高、速度越快，得分越高。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds.In each round, the athletes completed the 1 poker cards in 15 minutes (excluding the red joker and black joker). The higher the correct rate, the faster the speed, the higher the score.
　　4、本项目比赛结束后，系统自动统计并公布本项目所有选手和战队的成绩。
　　4. After the end of this discipline, the system automatically calculates and releases the result.
　　
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div> <?=__('评判标准', 'nlyd-student')?></div>
<span class="c_blue">获胜依据：</span>
<span class="c_blue">Scoring rules:</span>
　　1.数量分：正确记忆1张牌得18分；
　　1. Quantity points: Correctly memorizing 1 card for 18 points;
　　2.时间分：在15分钟内复牌提交后剩余1秒得1分。
　　2. Time points: 1 points for the remaining 1 second after the submission in 15 minutes.
　　3.总分高者胜，总分相同则用时少者胜。
　　3. A high score wins, if the total score is the same, less time wins.

<span class="c_blue">选手成绩：</span>
<span class="c_blue">Personal score:</span>
　　1.当选手记忆扑克有错误时，则选手得分=数量分。
　　1. When athlete’s answer is NOT completely right, the score is equal to the quantity points.
　　2.当选手记忆扑克全部正确时，则选手得分=数量分+时间分。
　　2. When athlete’s answer is completely right, the score is equal to the quantity points plus time points.
　　3.取最高分轮次的成绩参与排名。
　　3. The highest round score is the final score.

<span class="c_blue">战队成绩：</span>
<span class="c_blue">Team score:</span>
　　以战队各轮前五名选手总分之和为战队得分。
　　The total score of top 5 athletes from each team is the final team score.


                <?php
                    break;
                case 'kysm': ?>
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <?=__('项目意义', 'nlyd-student')?></div>
　　快眼扫描是训练快速准确感知文字、符号、数字等信息的重要项目，感知信息量每两题递增一次，无限增多，能有效提高注意力和视觉感知力。本项目从一项特工瞬间观察力训练演化而来，对提高实际生活中瞬间准确感知大量信息的能力具有重要意义。
　　Fast eye scan is an important item in training to quickly and accurately perceive text, symbols, numbers and other information. The amount of perceived information increases every two questions, infinitely, which can effectively improve attention and visual perception. This project evolved from a spy's instantaneous observation training, which is of great significance to improve the ability of instantaneous and accurate perception of large amounts of information in real life.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div> <?=__('比赛规程', 'nlyd-student')?></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2. All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。每轮题目显示的随机信息数量由少到多逐渐增加，每一行最多显示30个信息，满行后行数逐渐增加。信息包含数字、字母、符号、文字等。每道题闪现时间0.8秒，之后列出6个不同选项，选手在5秒内选出其中1个与刚才闪现的信息一致的选项。当选错数量达到10个时，该选手本轮比赛结束。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds. The number of random information displayed on each round is gradually increased from less to more, with a maximum of 30 characters per line, and a gradual increase in the number of rows after the full line.Information contains number, letter, symbol, word and so on. Each question flashes for 0.8 seconds, then lists 6 different options, and the athlete selects 1 options within 5 seconds to match the message that has just flashed. It ends when the athlete accumulatively errors 10 times.
　　4、待所有选手答题结束后，系统自动统计并公布本项目所有选手和战队的成绩。
　　4. After the end of this discipline, the system automatically calculates and releases the result.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div> <?=__('评判标准', 'nlyd-student')?></div>
<span class="c_blue">获胜依据：</span>
<span class="c_blue">Scoring rules:</span>
　　1.在每题6个不同选项中只有1个正确答案，选对1题得10分。
　　1. There are only 1 correct answer in 6 different options, and 10 points for each correct one.
　　2.总分高者胜，总分相同则用时少者胜。
　　2. A high score wins, if the total score is the same, less time wins.

<span class="c_blue">选手成绩：</span>
<span class="c_blue">Personal score:</span>
　　1.选手得分=正确答题数×10。
　　1. The score is quantity of right answer multiplied by 10.
　　2.取最高分轮次的成绩参与排名。
　　2. The highest round score is the final score.

<span class="c_blue">战队成绩：</span>
<span class="c_blue">Team score:</span>
　　以战队各轮前五名选手总分之和为战队得分。
　　The total score of top 5 athletes from each team is the final team score.


                <?php
                    break;
                case 'wzsd': ?>
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <?=__('项目意义', 'nlyd-student')?></div>
　　阅读是获取知识的重要方式，文章速读是注意力、文字感知力、理解力、记忆力的重要训练项目，是提高获取知识速度和准确性、提高阅读效率的有效手段。
　　Reading is an important way to acquire knowledge. Speed reading is an important training item for attention, text perception, comprehension and memory. It is an effective means to improve the speed and accuracy of knowledge acquisition and improve reading efficiency.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div> <?=__('比赛规程', 'nlyd-student')?></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2.  All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。每轮比赛速读1篇2000字左右文章，选手阅读文章后，点击“开始答题”，系统自动给出10道单项选择题测试理解率，选手须在15分钟内完成阅读和答题。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds. Athlete reads an article about 2000 words in each round, after reading, then click the "begin to answer" button, and answer 10 questions within 15 minutes.
　　4、待所有选手答题结束后，系统自动统计并公布本项目所有选手和战队的成绩。
　　4. After the end of this discipline, the system automatically calculates and releases the result.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div> <?=__('评判标准', 'nlyd-student')?></div>
<span class="c_blue">获胜依据：</span>
<span class="c_blue">Scoring rules:</span>
　　1.数量分：选择正确1题得23分；
　　1. Quantity points: 23 points for each right answer;
　　2.时间分：在15分钟内完成阅读和答题后剩余1秒得1分。
　　2. Time points: 1 points for the remaining 1 second after the submission in 15 minutes.
　　3.总分高者胜，总相同则用时少者胜。
　　3. A high score wins, if the total score is the same, less time wins.

<span class="c_blue">选手成绩：</span>
<span class="c_blue">Personal score:</span>
　　1.当选手答题有错时，则选手得分=数量分。
　　1. When athlete’s answer is NOT completely right, the score is equal to the quantity points.
　　2.当选手答题全部正确时，则选手得分=数量分+时间分。
　　2. When athlete’s answer is completely right, the score is equal to the quantity points plus time points.
　　3.取最高分轮次的成绩参与排名。
　　3. The highest round score is the final score.

<span class="c_blue">战队成绩：</span>
<span class="c_blue">Team score:</span>
　　以战队各轮前五名选手总分之和为战队得分。
　　The total score of top 5 athletes from each team is the final team score.


                <?php
                    break;
                case 'zxss': ?>
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <?=__('项目意义', 'nlyd-student')?></div>
　　计算是大脑数理逻辑推理能力的综合应用，是理解力水平最直观的体现。正向速算是综合训练大脑注意力、理解力和记忆力的重要项目。本项目不同于奥数、珠心算等技巧性比赛，侧重于面向普通人通过简单的加减乘除运算考查大脑计算的速度和准确性，重在脑力素质的训练。
　　Computation is the comprehensive application of mathematical logic reasoning ability of the brain, and it is the most direct embodiment of comprehension level. Fast calculation is an important project to train brain's attention, comprehension and memory. This project is different from the Olympic mathematics competition, abacus arithmetic and other technical competitions, focusing on ordinary people through simple addition, subtraction, multiplication and division to check the speed and accuracy of brain computing, focusing on the training of mental quality.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div> <?=__('比赛规程', 'nlyd-student')?></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2. All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。.每轮比赛中，选手依次进行连加运算（最多5个加数且每个加数最多4位数）、加减混合运算（最多5个加减数且每个加减数最多4位数）和乘除运算（除数和1个乘数为1位数且另一位乘数或被除数最多4位数）三个项目的比拼，每个项目限时3分钟，系统分别逐题给出，选手提交答案后自动给出下一题，不限题目数量，答对题数越多得分越高。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds． In each round, the athlete need to complete 3 types of topics, including Addition, the Addition and Subtraction, the Multiplication and Division, each type is 3 minutes, the system gives questions one by one, the quantity of question is no-limit.
　　4、系统自动统计并公布本项目所有选手和战队的成绩。
　　4. The system automatically calculates and releases the result.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div> <?=__('评判标准', 'nlyd-student')?></div>
<span class="c_blue">获胜依据：</span>
<span class="c_blue">Scoring rules:</span>
　　1.答对1题计10分。
　　1. 10 points for each right answer.
　　2.总分高者胜，总分相同则正确率高者胜。
　　2. A high score wins, if the score is the same, high correct rate wins.

<span class="c_blue">选手成绩：</span>
<span class="c_blue">Personal score:</span>
　　1.选手得分=正确答题数×10。
　　1. The score is quantity of right answer multiplied by 10.
　　2.取最高分轮次的成绩参与排名。
　　2. The highest round score is the final score.

<span class="c_blue">战队成绩：</span>
<span class="c_blue">Team score:</span>
　　以战队各轮前五名选手总分之和为战队得分。
　　The total score of top 5 athletes from each team is the final team score.


                <?php
                    break;
                case 'nxss': ?>
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <?=__('项目意义', 'nlyd-student')?></div>
　　逆向速算来源于著名的“24点智力游戏”，通过把系统出示的0-13中的4个数据用加、减、乘、除和括号连接成算式，使其计算结果等于24，综合训练大脑的注意力、发散思维、逆向思维、想象力和记忆力。
　　24-point originates from the famous "24-point game". By adding, subtracting, multiplying, dividing and bracketing four data from 0-13 produced by the system into an arithmetic formula, the result is equal to 24. It integrates training of the brain's attention, divergent thinking, reverse thinking, imagination and memory.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div> <?=__('比赛规程', 'nlyd-student')?></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2. All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。系统逐一出题，每题给出1～13之中的4个数据，选手使用加、减、乘、除运算符号和括号把它们连成一个算式，使其运算结果等于24。每轮比赛10分钟，不限题目数量，不答而直接点击“下一题”则减少剩余答题时间2秒，答对题数越多分数越高。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds.The system comes out one by one. Each question is given 4 digit from 1to13. Athlete use add, subtract, multiply, divide and brackets to connect them into one formula equal to 24. Each round is 10 minutes, the quantity of questions is no-limit, the remaining time is reduced by 2 seconds for each  wrong answer or not answer directly click "next question", more answer questions wins.
　　4、待所有选手答题结束后，系统自动统计并公布本项目所有选手和战队的成绩。
　　4. After the end of this discipline, the system automatically calculates and releases the result.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div> <?=__('评判标准', 'nlyd-student')?></div>
<span class="c_blue">获胜依据：</span>
<span class="c_blue">Scoring rules:</span>
　　1.答对1题得10分。
　　1. 10 points for each right answer.
　　2.总分高者胜，总分相同则正确率高者胜。
　　2. A high score wins, if the score is the same, high correct rate wins.

<span class="c_blue">选手成绩：</span>
<span class="c_blue">Personal score:</span>
　　1.选手得分=正确答题数×10。
　　1. The score is quantity of right answer multiplied by 10.
　　2.取最高分轮次的成绩参与排名。
　　2. The highest round score is the final score.

<span class="c_blue">战队成绩：</span>
<span class="c_blue">Team score:</span>
　　以战队各轮前五名选手总分之和为战队得分。
　　The total score of top 5 athletes from each team is the final team score.


                <?php
                    break;
                default:
                ?>

        <?php } ?>
            </pre>
        </div>
    </div>
    <a class="a-btn a-btn-table" id="go" href="<?=home_url('trains/initial/genre_id/'.$_GET['genre_id'].'/type/'.$_GET['type'].'/match_more/1')?>"><div><?=__('开始训练', 'nlyd-student')?></div></a>
</div>

<script>
jQuery(function($) { 
    $('#go').click(function(){
        $.DelSession('train_match')
    })
})
</script>
