
function tdisoper(f0,f1,f2,f3)
{
this[0]=f0;
this[1]=f1;
this[2]=f2;
this[3]=f3;
}
disoper=new tdisoper("-","+","/","*");
function oper(f,m,n)
{
if (f==3) return(m*n);
if (f==2) return(m/n);
if (f==1) return(parseFloat(m)+parseFloat(n));
if (f==0) return(m-n);
}
function tb(i1,i2,i4,i8)
{
this[1]=i1;
this[2]=i2;
this[4]=i4;
this[8]=i8;
}
function valid(F)
{
n=1;//找出几个大案，0为全部
b=new tb(F[0],F[1],F[2],F[3]);
k=0;
result="";
for (i1=1;i1<=8;i1*=2)
 for (i2=1;i2<=8;i2*=2)
   for (i3=1;i3<=8;i3*=2)
     for (i4=1;i4<=8;i4*=2)
       {
        if ((i1|i2|i3|i4)!=0xf) continue;
          for (f1=0;f1<=3;f1++)
            for (f2=0;f2<=3;f2++)
              for (f3=0;f3<=3;f3++)
                   {
						m=oper(f3,oper(f2,oper(f1,b[i1],b[i2]) ,b[i3] ) ,b[i4]);
						if (Math.abs(m-24)<1e-5 ) {
							result=result+"(("+b[i1]+disoper[f1]+b[i2]+")"+disoper[f2]+b[i3]+")"+disoper[f3]+b[i4]+"\n";
							return 	result
							if ((n!=0)&&(++k>=n)) return(false);
						}
						m=oper(f1, b[i1], oper(f3, oper(f2,b[i2],b[i3]) ,b[i4]) );
						if (Math.abs(m-24)<1e-5){
							result=result+b[i1]+disoper[f1]+"(("+b[i2]+disoper[f2]+b[i3]+")"+disoper[f3]+b[i4]+")\n";
							return 	result
							if ((n!=0)&&(++k>=n)) return(false);
						}
						m=oper(f3,oper(f1,b[i1], oper(f2,b[i2],b[i3]) ),b[i4]);
						if (Math.abs(m-24)<1e-5){
							result=result+"("+b[i1]+disoper[f1]+"("+b[i2]+disoper[f2]+b[i3]+"))"+disoper[f3]+b[i4]+"\n";
							return 	result
							if ((n!=0)&&(++k>=n)) return(false);
						}
						m=oper(f1, b[i1], oper(f2, b[i2], oper(f3, b[i3], b[i4]) ) );
						if (Math.abs(m-24)<1e-5){
                            result=result+b[i1]+disoper[f1]+"("+b[i2]+disoper[f2]+"("+b[i3]+disoper[f3]+b[i4]+"))\n";
                            return 	result
							if ((n!=0)&&(++k>=n)) return(false);
						}
						m=oper(f2,oper(f1,b[i1],b[i2]), oper(f3,b[i3],b[i4]) );
						if (Math.abs(m-24)<1e-5){
							result=result+"("+b[i1]+disoper[f1]+b[i2]+")"+disoper[f2]+"("+b[i3]+disoper[f3]+b[i4]+")\n";
							return 	result
							if ((n!=0)&&(++k>=n)) return(false);
						}

           			}
		  }
		 
    // result=result+"----找全了!----\n"
    result="本题无解"
	return 	result
	return(false);
}





//-----------------------计算器----------------------------
	  	/*function calculateResult(expression) {
	    //    new_expression = "1.1+(2.1*3)-(((2.2*3)*((1.1+2.2)/3.3)-3.4*6)-(3.5*6))+3.6";
			var new_expression=expression;
			var wrongMessage = 'false';
			if (!checkOperator(new_expression) || !checkFloat(new_expression)) {
				new_expression = '';
				return wrongMessage;
			}
			if(hasBracket(new_expression)) {
				if (!checkBracket(new_expression)) {
					new_expression = '';
					return wrongMessage;
				}
				new_expression = removeBracket(new_expression);
			}
	 
	 
			if (hasMultiplicOrDivision(new_expression)) {
				new_expression = removeMultiplicOrDivision(new_expression);
			}
			if(hasPlusOrMinus(new_expression)) {
				new_expression = plusOrMinus(new_expression);
			}
			return new_expression
		};*/
		function calculateResult(content){
	        try {
	            return eval(content); // no exception occured
	        } 
	        catch (e) {
	            if (e instanceof SyntaxError) { // Syntax error exception
	                return 'Syntax error exception'; // exception occured
	            }
	            else {// Unspecified exceptions
	                return 'Unspecified exceptions'; // exception occured
	            }
	        }
    	}

		function hasPlusOrMinus(expression) {
			var hasPlusOrMinusReg =  /(\+|\-)/;
			if (hasPlusOrMinusReg.test(expression)) {
				return true;
			};
			return false;
		};
		function checkOperator(expression) {
			/**
			 * (^[\+\*\/])|([\+\-\*\/]$) 匹配首字符或是最后一个字符是不是运算符
			 * ([\+\-\*\/][\+\*\/]+) [+,*,-,/]搭配一个或是多个[+,*,/]
			 * ([\*\/](\-)+) [*,/]搭配一个或是多个[-]
			 * ([\+\-](\-){2,}) [+,-]搭配两个以上的[-]
			 * @type RegExp
			 */
		  var reg = /(^[\+\*\/])|([\+\-\*\/]$)|([\+\-\*\/][\+\*\/]+)|([\*\/](\-)+)|([\+\-](\-){2,})/;  
		  if(reg.test(expression)) {
			  return false;
		  }
		  return true;
		};
		 function checkFloat(expression) {
			/**
			 * (^\.)|(\.$) expression以.开头或结尾
			 * ([\+\-\*\/]\.)|(\.[\+\-\*\/]) expression出现".+","+."等情况
			 * ((\d+\.+){2,}\d*) expression出现"2.2.3.4.5","..","..."等情况
			 */
			var reg = /(^\.)|(\.$)|([\+\-\*\/]\.)|(\.[\+\-\*\/])|((\d+\.+){2,}\d*)/;
			if (reg.test(expression)) {
				return false;
			}
			return true;
		};
		function hasBracket(expression) {
			var hasBraketReg = /(\(|\))/; //查看表达expression中是否有（）；
			if (hasBraketReg.test(expression)) {
				return true;
			}
			return false;
		};
		function checkBracket(expression) {
			var reg = /([\d\.]\()|(\)[\d\.])|(\([\+\-\*\/\.])|([\+\-\*\/\.]\))/;
			if (reg.test(expression)) {
				return false;
			}
			var leftBracket = 0, rightBracket = 0;
			for (var i = 0; i < expression.length; i++) {
				if (expression.charAt(i) === "(") {
					leftBracket++;
				} else if(expression.charAt(i) === ")") {
					rightBracket++;
				}
			}
			if (!(leftBracket === rightBracket)) {
				return false;
			}
			return true;
		};
		function removeBracket(expression) {
			var regMatch = /\([^\(\)]+\)/g; //这个正则表达式匹配最里面的一层括号，既(这里面是不能包含“（”或是“）”的)
			var childExpression = expression.match(regMatch);
			var mySign = "&mySign&"; //用一个特殊标记记录等会需要替换的位置
			var replaceExpression = expression.replace(regMatch,mySign);
			var subExpression = "";
			for(var i = 0; i < childExpression.length; i++) {
				subExpression = childExpression[i].substring(1,childExpression[i].length-1);
				if (hasMultiplicOrDivision(subExpression)) {
					subExpression = removeMultiplicOrDivision(subExpression);
				}
				if (hasPlusOrMinus(subExpression)) {
					subExpression = plusOrMinus(subExpression);
				}
				replaceExpression = replaceExpression.replace(mySign,subExpression);
			}
			expression = replaceExpression;
			if(hasBracket(expression)) {
				expression = removeBracket(expression);
			} else {
				return expression;
			}
			return expression;
		};
		function hasMultiplicOrDivision(expression) {
			var hasMultiplicOrDivisionReg =  /(\*|\/)/;
			if (hasMultiplicOrDivisionReg.test(expression)) {
				return true;
			};
			return false;
		};
		 function removeMultiplicOrDivision(expression) {
	//        var expression = "1-13*2+3*456/3*15*8/5*4-50-40+32*2/16"; //test data
			var regMatch = /((\d+\.?\d*)(\*|\/))+(\d+\.?\d*)/g;
			var childResult = "";
			var childExpression = expression.match(regMatch);
			var mySign = "&mySign&"; //用一个特殊标记记录等会需要替换的位置
			var replaceExpression = expression.replace(regMatch,mySign);
			for(var i = 0; i < childExpression.length; i++) {
				childResult = multiplicOrDivision(childExpression[i]);
				replaceExpression = replaceExpression.replace(mySign,childResult);
			}
			expression = replaceExpression;
			return expression;
		};
		 function multiplicOrDivision(expression) {
			var regNumber = /\d+\.?\d*/g;
			var regOperator = /(\*|\/)/g;
			var arrNumbers = expression.match(regNumber);
			var arrOperators = expression.match(regOperator);
			
			var calResult = parseFloat(arrNumbers[0]);
			for (var i = 0; i < arrOperators.length; i++) {
				if (arrOperators[i] === '*') {
					calResult *= parseFloat(arrNumbers[i+1]);
				} else {
					calResult /= parseFloat(arrNumbers[i+1]);
				}
			}
			return calResult;
		};
		function plusOrMinus(expression) {
			/**
			 *  如果第一个字符是“-”号,就在expression前面加上一个0即可
			 */
			if (expression.charAt(0) === "-") {
				expression = 0 + expression;
			}
			var doubleMinusReg = /\-\-/;  //减去一个负数，等于加一个正数
			if (doubleMinusReg.test(expression)) {
				expression = expression.replace(doubleMinusReg,"+");
			}
			doubleMinusReg = /\+\-/;  //加上一负数，等于减去一个正数
			if (doubleMinusReg.test(expression)) {
				expression = expression.replace(doubleMinusReg,"-");
			}
			var regNumber = /\d+\.?\d*/g;
			var regOperator = /(\+|\-)/g;
			var arrNumbers = expression.match(regNumber);
			var arrOperators = expression.match(regOperator);
			
			var calResult = parseFloat(arrNumbers[0]);
			for (var i = 0; i < arrOperators.length; i++) {
				if (arrOperators[i] === '+') {
					calResult += parseFloat(arrNumbers[i+1]);
				} else {
					calResult -= parseFloat(arrNumbers[i+1]);
				}
			}
			return calResult;
		};