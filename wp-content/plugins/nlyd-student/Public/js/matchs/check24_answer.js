
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