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
.kaoji_list li a{
    display:block;
    width:80%;
    margin:0 auto 10px auto;
    padding:2px 0;
    background:#ebf4ff;
    text-align:center;
    color:#85868c;
    border-radius:3px;
}
.layui-layer-content{
    padding:10px;
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
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <div class="title_name"><?=__('项目意义', 'nlyd-student')?></div></div>
　　数字是世界公认最难记忆的信息，但运用记忆术可以轻松克服这一难题，同时对大脑的注意力、记忆力、创造力和敏锐度也是一个有效的训练。本赛事向广大群众提供公益性的记忆术培训，掌握技术方法之后利用本训练平台进行自我训练，记忆水平将大幅提高。
　　Numbers are universally acknowledged to be the most difficult information to remember, but memory is an easy way to overcome this problem and an effective training for the brain's attention, memory, creativity and sharpness. This competition provides public welfare memory training to the masses. After mastering the techniques and methods, the training platform will be used for self-training, and the memory level will be greatly improved.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div> <div class="title_name"><?=__('比赛规程', 'nlyd-student')?></div></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2. All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。每轮比赛选手在20分钟内完成100个随机数字的记忆和复位，正确率越高、速度越快，得分越高。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds. In each round, 100 random numbers are remembered and reset in 20 minutes. The higher the correct rate, the faster the speed, the higher the score.
　　4、待所有选手答题结束后，系统自动统计并公布本项目所有选手和战队的成绩。
　　4. After the end of this discipline, the system automatically calculates and releases the result.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div>  <div class="title_name"><?=__('评判标准', 'nlyd-student')?></div></div>
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
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <div class="title_name"><?=__('项目意义', 'nlyd-student')?></div></div>
　　扑克牌是训练多元素信息记忆能力的极佳工具，训练者要在尽量短的时间内记住一副牌的颜色、图案、字符、顺序等多项信息，各元素的搭配要准确无误，是训练注意力、视觉感知力、记忆力和创造力（想象力）的重要方式。
　　Poker is an excellent tool for training multi-element information memory ability. Trainers should remember as soon as possible the color, pattern, character, sequence and other information of a deck of cards. The combination of various elements must be accurate. It is an important way to train attention, visual perception, memory and creativity (imagination).

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div>  <div class="title_name"><?=__('比赛规程', 'nlyd-student')?></div></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2. All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。每轮比赛选手在15分钟内完成1副扑克牌（不含大小王共52张）记忆和复牌，正确率越高、速度越快，得分越高。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds.In each round, the athletes completed the 1 poker cards in 15 minutes (excluding the red joker and black joker). The higher the correct rate, the faster the speed, the higher the score.
　　4、本项目比赛结束后，系统自动统计并公布本项目所有选手和战队的成绩。
　　4. After the end of this discipline, the system automatically calculates and releases the result.
　　
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div> <div class="title_name"><?=__('评判标准', 'nlyd-student')?></div></div>
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
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <div class="title_name"><?=__('项目意义', 'nlyd-student')?></div></div>
　　快眼扫描是训练快速准确感知文字、符号、数字等信息的重要项目，感知信息量每两题递增一次，无限增多，能有效提高注意力和视觉感知力。本项目从一项特工瞬间观察力训练演化而来，对提高实际生活中瞬间准确感知大量信息的能力具有重要意义。
　　Fast eye scan is an important item in training to quickly and accurately perceive text, symbols, numbers and other information. The amount of perceived information increases every two questions, infinitely, which can effectively improve attention and visual perception. This project evolved from a spy's instantaneous observation training, which is of great significance to improve the ability of instantaneous and accurate perception of large amounts of information in real life.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div> <div class="title_name"><?=__('比赛规程', 'nlyd-student')?></div></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2. All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。每轮题目显示的随机信息数量由少到多逐渐增加，每一行最多显示30个信息，满行后行数逐渐增加。信息包含数字、字母、符号、文字等。每道题闪现时间0.8秒，之后列出6个不同选项，选手在5秒内选出其中1个与刚才闪现的信息一致的选项。当选错数量达到10个时，该选手本轮比赛结束。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds. The number of random information displayed on each round is gradually increased from less to more, with a maximum of 30 characters per line, and a gradual increase in the number of rows after the full line.Information contains number, letter, symbol, word and so on. Each question flashes for 0.8 seconds, then lists 6 different options, and the athlete selects 1 options within 5 seconds to match the message that has just flashed. It ends when the athlete accumulatively errors 10 times.
　　4、待所有选手答题结束后，系统自动统计并公布本项目所有选手和战队的成绩。
　　4. After the end of this discipline, the system automatically calculates and releases the result.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div> <div class="title_name"><?=__('评判标准', 'nlyd-student')?></div></div>
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
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <div class="title_name"><?=__('项目意义', 'nlyd-student')?></div></div>
　　阅读是获取知识的重要方式，文章速读是注意力、文字感知力、理解力、记忆力的重要训练项目，是提高获取知识速度和准确性、提高阅读效率的有效手段。
　　Reading is an important way to acquire knowledge. Speed reading is an important training item for attention, text perception, comprehension and memory. It is an effective means to improve the speed and accuracy of knowledge acquisition and improve reading efficiency.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div> <div class="title_name"><?=__('比赛规程', 'nlyd-student')?></div></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2.  All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。每轮比赛速读1篇2000字左右文章，选手阅读文章后，点击“开始答题”，系统自动给出10道单项选择题测试理解率，选手须在15分钟内完成阅读和答题。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds. Athlete reads an article about 2000 words in each round, after reading, then click the "begin to answer" button, and answer 10 questions within 15 minutes.
　　4、待所有选手答题结束后，系统自动统计并公布本项目所有选手和战队的成绩。
　　4. After the end of this discipline, the system automatically calculates and releases the result.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div> <div class="title_name"><?=__('评判标准', 'nlyd-student')?></div></div>
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
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <div class="title_name"><?=__('项目意义', 'nlyd-student')?></div></div>
　　计算是大脑数理逻辑推理能力的综合应用，是理解力水平最直观的体现。正向速算是综合训练大脑注意力、理解力和记忆力的重要项目。本项目不同于奥数、珠心算等技巧性比赛，侧重于面向普通人通过简单的加减乘除运算考查大脑计算的速度和准确性，重在脑力素质的训练。
　　Computation is the comprehensive application of mathematical logic reasoning ability of the brain, and it is the most direct embodiment of comprehension level. Fast calculation is an important project to train brain's attention, comprehension and memory. This project is different from the Olympic mathematics competition, abacus arithmetic and other technical competitions, focusing on ordinary people through simple addition, subtraction, multiplication and division to check the speed and accuracy of brain computing, focusing on the training of mental quality.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div> <div class="title_name"><?=__('比赛规程', 'nlyd-student')?></div></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2. All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。.每轮比赛中，选手依次进行连加运算（最多5个加数且每个加数最多4位数）、加减混合运算（最多5个加减数且每个加减数最多4位数）和乘除运算（除数和1个乘数为1位数且另一位乘数或被除数最多4位数）三个项目的比拼，每个项目限时3分钟，系统分别逐题给出，选手提交答案后自动给出下一题，不限题目数量，答对题数越多得分越高。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds． In each round, the athlete need to complete 3 types of topics, including Addition, the Addition and Subtraction, the Multiplication and Division, each type is 3 minutes, the system gives questions one by one, the quantity of question is no-limit.
　　4、系统自动统计并公布本项目所有选手和战队的成绩。
　　4. The system automatically calculates and releases the result.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div> <div class="title_name"><?=__('评判标准', 'nlyd-student')?></div></div>
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
<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/xmyy.png'?>"></div> <div class="title_name"><?=__('项目意义', 'nlyd-student')?></div></div>
　　逆向速算来源于著名的“24点智力游戏”，通过把系统出示的0-13中的4个数据用加、减、乘、除和括号连接成算式，使其计算结果等于24，综合训练大脑的注意力、发散思维、逆向思维、想象力和记忆力。
　　24-point originates from the famous "24-point game". By adding, subtracting, multiplying, dividing and bracketing four data from 0-13 produced by the system into an arithmetic formula, the result is equal to 24. It integrates training of the brain's attention, divergent thinking, reverse thinking, imagination and memory.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/bsgc.png'?>"></div> <div class="title_name"><?=__('比赛规程', 'nlyd-student')?></div></div>
　　1、选手在“国际脑力运动”线上平台报名参加脑力世界杯并选中本项比赛。
　　1. Athlete register  Intellectual World Cup on the "International Intellectual Sports" online platform and select this discipline.
　　2、所有选手在“国际脑力运动”线上平台上点击进入本项目比赛倒计时页面，倒计时归零后立即开始比赛。
　　2. All athletes clicked into the countdown page on the "International Intellectual Sports" online platform, and the competition began immediately after the countdown returned to zero.
　　3、比赛分三轮进行，每两轮比赛之间开展中场活动。系统逐一出题，每题给出1～13之中的4个数据，选手使用加、减、乘、除运算符号和括号把它们连成一个算式，使其运算结果等于24。每轮比赛10分钟，不限题目数量，不答而直接点击“下一题”则减少剩余答题时间2秒，连续作答“本题无解”不可超过5次，否则系统将强制提交本轮成绩，答对题数越多分数越高。
　　3. The game is carried out in three rounds, and midfield activities are carried out between the two rounds.The system comes out one by one. Each question is given 4 digit from 1to13. Athlete use add, subtract, multiply, divide and brackets to connect them into one formula equal to 24. Each round is 10 minutes, the quantity of questions is no-limit, the remaining time is reduced by 2 seconds for not answer directly click "next question",continuous answer "unsolvable" can not be more than 5 times, otherwise the system will be mandatory to submit the results of this round, more answer questions wins.
　　4、待所有选手答题结束后，系统自动统计并公布本项目所有选手和战队的成绩。
　　4. After the end of this discipline, the system automatically calculates and releases the result.

<div class="bold ta_c c_blue fs_16"><div class="img-box ready_img"><img src="<?=student_css_url.'image/trains/ppbz.png'?>"></div> <div class="title_name"><?=__('评判标准', 'nlyd-student')?></div></div>
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
                case 'memory': ?>
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
                case 'reading': ?>
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
                case 'arithmetic': ?>
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
    <?php
        $url = home_url('trains/initial/genre_id/'.$_GET['genre_id'].'/type/'.$_GET['type'].'/match_more/1');
        switch ($_GET['type']){
            case 'reading':
                $url = home_url('grade/initial/genre_id/'.$_GET['genre_id'].'/grad_type/'.$_GET['type'].'/');
                break;
            case 'memory':
                break;
            case 'arithmetic':
                $url = home_url('grade/initial/genre_id/'.$_GET['genre_id'].'/grad_type/'.$_GET['type'].'/type/zxys');
                break;
        }
    ?>
    <a class="a-btn a-btn-table" id="go" href="<?=$url?>"><div><?=__('开始训练', 'nlyd-student')?></div></a>


</div>
<script>
jQuery(function($) { 
    $.DelSession('train_match');//准备页面题目
    $.DelSession('_match_train');//答题页面记录的题目（无准备页）

    var _type=$.Request('type');
    layui.use('layer', function(){
        if(_type=='memory'){
            $('#go').click(function(){
                var dom='<ul class="kaoji_list">'
                            +'<li>'
                                +'<a href=""><?=__('记忆一级', 'nlyd-student')?></a>'
                            +'</li>'
                            +'<li>'
                                +'<a href=""><?=__('记忆二级', 'nlyd-student')?></a>'
                            +'</li>'
                            +'<li>'
                                +'<a href=""><?=__('记忆三级', 'nlyd-student')?></a>'
                            +'</li>'
                            +'<li>'
                                +'<a href=""><?=__('记忆四级', 'nlyd-student')?></a>'
                            +'</li>'
                            +'<li>'
                                +'<a href=""><?=__('记忆五级', 'nlyd-student')?></a>'
                            +'</li>'
                            +'<li>'
                                +'<a href=""><?=__('记忆六级', 'nlyd-student')?></a>'
                            +'</li>'
                            +'<li>'
                                +'<a href=""><?=__('记忆七级', 'nlyd-student')?></a>'
                            +'</li>'
                            +'<li>'
                                +'<a href=""><?=__('记忆八级', 'nlyd-student')?></a>'
                            +'</li>'
                            +'<li>'
                                +'<a href=""><?=__('记忆九级', 'nlyd-student')?></a>'
                            +'</li>'
                            +'<li>'
                                +'<a href=""><?=__('记忆十级', 'nlyd-student')?></a>'
                            +'</li>'
                        +'</ul>'
                layer.open({
                    type: 1
                    ,maxWidth:300
                    ,title: '<?=__('选择记忆级别', 'nlyd-student')?>' //不显示标题栏
                    ,skin:'nl-box-skin'
                    ,id: 'certifications' //防止重复弹出
                    ,content: dom
                    ,btn: []
                    ,success: function(layero, index){
                    }
                    ,yes: function(index, layero){
                        layer.closeAll();
                    }
                    ,btn2: function(index, layero){
                        layer.closeAll();
                        submit(1);
                    }
                    ,closeBtn:2
                    ,btnAagn: 'c' //按钮居中
                    ,shade: 0.3 //遮罩
                    ,isOutAnim:true//关闭动画
                });
                return false;
            })
        }
              
    });
    // if(_type=='memory'){
    //     $('#go').click(function(){
    //         console.log(1)
    //         return false;
    //     })
    // }
})
</script>
