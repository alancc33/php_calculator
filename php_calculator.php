<html>
<head>
<title>cal-111016044</title>
<style>
/*css按鈕樣式*/
input{
    border-radius: 10px;
    border-width: 1px;
    border-style: outset;
    height:55px;
    width:80px;
    font-size:20px;
}
</style>
</head>
<body>

<?php
session_start();
if(!isset($_SESSION['screen']))
{
    $_SESSION['screen']="";
    $_SESSION['input']="0";
    $_SESSION['number']="0";
    $_SESSION['result']="0";
    $_SESSION['ans']="0";
    $_SESSION['op']=false;
    $_SESSION['per']=false;
    $_SESSION['error']=false;
    $_SESSION['end']=false;
    $_SESSION['dot']=false;
    $_SESSION['sc']="";
}
$screen=$_SESSION['screen'];
$input=$_SESSION['input'];
$number=$_SESSION['number'];
$result=$_SESSION['result'];
$ans=$_SESSION['ans'];
$op=$_SESSION['op'];
$per=$_SESSION['per'];
$error=$_SESSION['error'];
$end=$_SESSION['end'];
$dot=$_SESSION['dot'];
$sc=$_SESSION['sc'];

//傳入字串將數字化簡並傳回
function cleannumber($clnum)
{
    if(strpos($clnum,".")==false)
    {
        return $clnum;
    }
    for($i=strlen($clnum)-1;$i>0;$i--)
    {
        if(substr($clnum,$i,1)!="0")
        {
            if(substr($clnum,$i,1)==".")
            {
                $clnum=substr($clnum,0,$i);
            }
            else
            {
                $clnum=substr($clnum,0,$i+1);
            }
            break;
        }
    }
    return $clnum;
}
//數字鍵
if(!is_null($_POST['numbtn']))
{
    $nbtn=$_POST['numbtn'];
    if($error==true||$end==true)
    {
        $screen="";
        $input=$nbtn;
        $end=false;
        $error=false;
    }
    else if($op==true||$per==true)
    {
        $input=$nbtn;
        $op=false;
        $per=false;
    }
    else
    {
        if($input=="0")
        {
            $input=$nbtn;
        }
        else
        {
            $input.=$nbtn;
        }
    }
}
//加減乘除鍵
if(!is_null($_POST['operbtn']))
{
    $opbtn=$_POST['operbtn'];
    if(!$error&&!$end)
    {
        if($screen=="")
        {
            $input=cleannumber($input);
            $screen=$input.$opbtn;
            $op=true;
        }
        //更換運算子
        else if($op==true)
        {
            $screen=$input.$opbtn;
        }
        //沒按等於直接將先前的兩數座運算
        else
        {
            $input=cleannumber($input);
            $result=substr($screen,0,strlen($screen)-1);
            $number=$input;
            $sc=substr($screen,strlen($screen)-1,1);
            switch($sc)
            {
                case '+':
                    $ans=$result+$number;
                    break;
                case '-':
                    $ans=$result-$number;
                    break;
                case '*':
                    $ans=$result*$number;
                    break;
                case '/':
                    try
                    {
                        $ans=$result/$number;
                    }
                    catch(Exception $ae)
                    {
                        if($number=="0")
                        {
                            $input="its can't be 0,bro";
                            $error=true;
                            $ans="0";
                        }
                        else
                        {
                            $ans=$result/$number;
                            $ans=substr($ans,0,16);
                        }
                    }
                    break;
                default:
            }
            if(!$error)
            {
                $input=cleannumber($ans);
                $screen=$input.$opbtn;
                $op=true;
            }
        }
    }
    else if($error)
    {
    }
    else
    {
        $screen=$input.$opbtn;
        $end=false;
        $op=true;
    }
}
//其他功能
if(!is_null($_POST['btn']))
{
    $bbtn=$_POST['btn'];
    switch($bbtn)
    {
        //清除鍵
        case 'C':
            $screen="";
            $input="0";
            $op=false;
            $end=false;
            $dot=false;
            $error=false;
            $per=false;
            break;
        //+/-鍵
        case '+/-':
            if(!$error)
            {
                if($end)
                {
                    $screen="";
                }
                $result=$input;
                $ans=$result*-1;
                $input=$ans;
            }
            break;
        //%鍵
        case '%':
            if(!$error)
            {
                $input=cleannumber($input);
                if($end)
                {
                    $screen="";
                    $result=$input;
                }
                else if($screen=="")
                {
                    $result="0";
                }
                else
                {
                    $result=substr($screen,0,strlen($screen)-1);
                }
                $number=$input;
                $ans=$result*($number/100);
                $input=$ans;
                $per=true;
                $op=false;
                $dot=false;
                $end=false;
            }
            break;
        //退格(清除)鍵
        case '<x|':
            if($end)
            {
                $screen="";
            }
            else if(!$op&&!$per&&!$error)
            {
                if(substr($input,strlen($input)-1,1)=='.')
                {
                    $dot=false;
                }
                if(strlen($input)>2)
                {
                    $input=substr($input,0,strlen($input)-1);
                }
                else if(strlen($input)==2)
                {
                    if(substr($input,0,1)=='-')
                    {
                        $input="0";
                    }
                    else
                    {
                        $input=substr($input,0,1);
                    }
                }
                else if(strlen($input)==1)
                {
                    $input="0";
                }
            }
            else if($error)
            {
                $screen="";
                $input="0";
                $end=false;
                $error=false;
            }
            break;
        //小數點
        case '.':
            if(!$dot&&!$error)
            {
                if($end)
                {
                    $screen="";
                    $input="0.";
                    $end=false;
                }
                else if($op)
                {
                    $input="0.";
                }
                else
                {
                    $input=$input.".";
                }
            }
            $dot=true;
            break;
        //清空(數字)鍵
        case 'CE':
            if($end||$error)
            {
                $screen="";                       
            }
            $input="0";
            $end=false;
            $dot=false;
            $error=false;
            $per=false;
            break;
        //倒數鍵
        case '1/x':
            if(!$error)
            {
                if($end)
                {
                    $screen="";
                }
                $input=cleannumber($input);
                $result="1";
                $number=$input;
                try
                {
                    $ans=$result/$number;
                }
                catch(Exception $ae)
                {
                    if($number=="0.0")
                    {
                        $input="its can't be 0,bro";
                        $error=true;
                        $ans=0;
                    }
                    else
                    {
                        $ans=$result/$number;
                        $ans=substr($ans,0,16);
                    }
                }
                $input=cleannumber($ans);
                $per=true;
                $op=false;
                $dot=false;
                $end=false;
            }
            break;
        //平方鍵
        case 'x^2':
            if(!$error)
            {
                if($end)
                {
                    $screen="";
                }
                $input=cleannumber($input);
                $number=$input;
                $number=$number*$number;
                $input=$number;
                $per=true;
                $op=false;
                $dot=false;
                $end=false;
            }
            break;
        //根號鍵
        case 'x^1/2':
            if(!$error)
            {
                if($end)
                {
                    $screen="";
                }
                $input=cleannumber($input);
                $number=$input;
                $number=sqrt($number);
                $ans=substr($number,0,16);
                $input=cleannumber($ans);
                $per=true;
                $op=false;
                $dot=false;
                $end=false;
            }
            break;
        //等於鍵
        case '=':   
            if($screen=="")
            {
            }
            else if(!$error&&!$end)
            {
                $input=cleannumber($input);
                $result=substr($screen,0,strlen($screen)-1);
                $number=$input;
                $sc=substr($screen,strlen($screen)-1,1);
                switch($sc)
                {
                    case '+':
                        $ans=$result+$number;
                        break;
                    case '-':
                        $ans=$result-$number;
                        break;
                    case '*':
                        $ans=$result*$number;
                        break;
                    case '/':
                        try
                        {
                            $ans=$result/$number;
                        }
                        catch(Exception $ae)
                        {
                            if($number=="0")
                            {
                                $input="its can't be 0,bro";
                                $error=true;
                                $ans="0";
                            }
                            else
                            {
                                $ans=$result/$number;
                                $ans=substr($ans,0,16);
                            }
                        }
                        break;
                    default:
                }
                if(!$error)
                {
                    $screen=$screen.$input."=";
                    $input=cleannumber($ans);
                    $end=true;
                }
            }
            //連續運算
            else if($end)
            {
                $input=cleannumber($input);
                $result=$input;
                switch($sc)
                {
                    case '+':
                        $ans=$result+$number;
                        break;
                    case '-':
                        $ans=$result-$number;
                        break;
                    case '*':
                        $ans=$result*$number;
                        break;
                    case '/':
                        try
                        {
                            $ans=$result/$number;
                        }
                        catch(Exception $ae)
                        {
                            if($number=="0")
                            {
                                $input="its can't be 0,bro";
                                $error=true;
                                $ans="0";
                            }
                            else
                            {
                                $ans=$result/$number;
                                $ans=substr($ans,0,16);
                            }
                        }
                        break;
                    default:
                }
                if(!$error)
                {
                    $screen=$input.$sc.$number."=";
                    $input=cleannumber($ans);
                    $end=true;
                }
            }
            break;
    }
}

$_SESSION['screen']=$screen;
$_SESSION['input']=$input;
$_SESSION['number']=$number;
$_SESSION['result']=$result;
$_SESSION['ans']=$ans;
$_SESSION['op']=$op;
$_SESSION['per']=$per;
$_SESSION['error']=$error;
$_SESSION['end']=$end;
$_SESSION['dot']=$dot;
$_SESSION['sc']=$sc;
?>
<CENTER>
<h1>Calculator</h1>
<h5>by <strong>Alan CC</strong></h5>
<hr size=2 noshade>
</CENTER>
<FORM action="cal-111016044.php" method=post>
	<table align="center" bgcolor="#f3f3f3" border="0">
        <tr>
        <!--螢幕-->
			<td align='right' valign="middle" colspan="4" style= "height:50px;font-size:10px;">
				<font  size="5" color="#8a8a8a" face="monospace">
					<?php echo $screen?>
				</font>
			</td>
		</tr>
		<tr>
			<td align='right' valign="middle" colspan="4" style= "height:50px;font-size:20px;">
				<font  size="6" color="#070707" face="monospace">
                    <?php echo $input?>
				</font>
			</td>
		</tr>
		<tr>
			<td align='center' valign="middle">
				<input style= "background-color:#f9f9f9;" type="submit" name="btn" value="%" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#f9f9f9;" type="submit" name="btn" value="CE" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#f9f9f9;" type="submit" name="btn" value="C" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#f9f9f9;" type="submit" name="btn" value="<x|" >
			</td>
		</tr>
		<tr>
			<td align='center' valign="middle">
				<input style= "background-color:#f9f9f9;" type="submit" name="btn" value="1/x" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#f9f9f9;" type="submit" name="btn" value="x^2" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#f9f9f9;" type="submit" name="btn" value="x^1/2" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#f9f9f9;" type="submit" name="operbtn" value="/" >
			</td>
		</tr>
		<tr>
			<td align='center' valign="middle">
				<input style= "background-color:#ffffff;" type="submit" name="numbtn" value="7" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#ffffff;" type="submit" name="numbtn" value="8" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#ffffff;" type="submit" name="numbtn" value="9" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#f9f9f9;" type="submit" name="operbtn" value="*" >
			</td>
		</tr>
		<tr>
			<td align='center' valign="middle">
				<input style= "background-color:#ffffff;" type="submit" name="numbtn" value="4" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#ffffff;" type="submit" name="numbtn" value="5" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#ffffff;" type="submit" name="numbtn" value="6" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#f9f9f9;" type="submit" name="operbtn" value="-" >
			</td>
		</tr>
		<tr>
			<td align='center' valign="middle" >
				<input style= "background-color:#ffffff;" type="submit" name="numbtn" value="1" >
			</td>
			<td align='center' valign="middle" >
				<input style= "background-color:#ffffff;" type="submit" name="numbtn" value="2" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#ffffff;" type="submit" name="numbtn" value="3" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#f9f9f9;" type="submit" name="operbtn" value="+" >
			</td>
		</tr>
        <tr>
			<td align='center' valign="middle" >
				<input style= "background-color:#ffffff;" type="submit" name="btn" value="+/-" >
			</td>
			<td align='center' valign="middle" >
				<input style= "background-color:#ffffff;" type="submit" name="numbtn" value="0" >
			</td>
			<td align='center' valign="middle">
				<input style= "background-color:#ffffff;" type="submit" name="btn" value="." >
			</td>
			<td align='center' valign="middle">
				<input style= "color:white;background-color:#7b43a4;" type="submit" name="btn" value="=" >
			</td>
		</tr>
	</table>
</FORM>
</body>
</html>