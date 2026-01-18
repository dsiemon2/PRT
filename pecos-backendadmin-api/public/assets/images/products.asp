<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pecos River Traders- Rugged Westernwear & Australian Outback</title>
<link rel='stylesheet' type='text/css' href='style.css'>
<style type="text/css">
<!--
.style1 {font-size: 14px}
.style2 {font-size: 12px}
.style3 {	color: #660000;
	font-weight: bold;
	font-size: 16px;
}
.style6 {color: #660000; font-weight: bold; font-size: 14px; }
-->
</style>
</head>
<body>
<!--#INCLUDE Virtual="/includes/adovbs.inc"-->
<!--#INCLUDE Virtual="/Common.asp"-->
<table border='0' cellpadding='0' cellspacing='0' width='770' background='images/tableBg.jpg' align='center'>
<!--#INCLUDE Virtual="/menu.asp"-->
<td width='215' align="right">
<a href='/default.asp'><img src='images/headerLeft.jpg' border='0'></a><br>
</td>
<td width='515' align='right' background='images/headerCenter.jpg' valign='top'>
<br />
<br /></span></a>
      </td>
<td width='40'>
<img src='images/headerRight.jpg'><br>
</td>
</tr>
<tr>
<td colspan='3'>
<img src='images/divider.jpg'><br>
</td>
</tr>
<tr>
<td colspan='3'>
	<table border='0' cellpadding='0' cellspacing='0' width='760' align='center'>
	<tr>
	<td width='195' valign='top'>	<div class='leftNav'>
	<% call MakeMenu%>
	<br>
	</td>
	<td width='20'>
	<img src='images/blank.gif' width='20' height='1'>
	</td>
	<td width='545' valign='top'>	<div style='width:535px;'>
	<!-- ************************** END HEADER *********** -->
<%
	intCatid=request("catid")
	txtCat=getCategory(intCatid)
	isSortable=ChkSort(intCatid)
%>
  
	
	<%call ShowProducts(intCatid)%>
	
	<p>&nbsp;</p>

	</div>
	</td>
	</tr>
	</table>
</td>
</tr>
<!--#INCLUDE Virtual="/Bot_Menu.asp"-->
</table>

<% 


function getCategory(intCatID)
	  txtsql="select * from Categories2 where CategoryCode=" & intCatID
	   'response.write txtsql
     'response.end
	  set dbCat2=CreateObject("ADODB.Recordset")
		'db.Open txtsql,dbconnect, adOpenDynamic, adLockUnspecified 
		dbCat2.Open txtsql,dbconnect
		
			 getCategory=dbcat2("category")
		dbcat2.close 
end function


function getResultCNT(intCatID)
	 txtsql="select count(*) as resultcnt from products3 where CategoryCode=" & intCatID 	
	   'response.write txtsql
     'response.end
	  set dbResultCNT=CreateObject("ADODB.Recordset")
		'db.Open txtsql,dbconnect, adOpenDynamic, adLockUnspecified 
		dbResultCNT.Open txtsql,dbconnect
		
			 getResultCNT=dbResultCNT("resultcnt")
		dbResultCNT.close 
end function

function chksort(intCatID)
	  txtsql="select * from Categories2 where CategoryCode=" & intCatID
	   'response.write txtsql
     'response.end
	  set dbCat2=CreateObject("ADODB.Recordset")
		'db.Open txtsql,dbconnect, adOpenDynamic, adLockUnspecified 
		dbCat2.Open txtsql,dbconnect
		  if dbCat2("IsOrdered")=True then 
		  	chksort=True
		  else 
		  	chksort=False
		  end if 
			
		dbcat2.close 
end function

function getCategoryDesc(intCatID)
	  txtsql="select * from Categories2 where CategoryCode=" & intCatID
	   'response.write txtsql
     'response.end
	  set dbCat2=CreateObject("ADODB.Recordset")
		'db.Open txtsql,dbconnect, adOpenDynamic, adLockUnspecified 
		dbCat2.Open txtsql,dbconnect
		
			 getCategoryDesc=dbcat2("ShrtDescription")
		dbcat2.close 
end function

Sub ShowVendorPic(intCatID)
	  txtsql="select * from Categories2 where CategoryCode=" & intCatID
	   'response.write txtsql
     'response.end
	  set dbCat2=CreateObject("ADODB.Recordset")
		'db.Open txtsql,dbconnect, adOpenDynamic, adLockUnspecified 
		dbCat2.Open txtsql,dbconnect
		
		if dbCat2("Distributorimage") <> ""  then 
		%>
			<center>
				<img src="/images/<%=dbCat2("Distributorimage")%>">
			</center>
		<%
	end if 
		dbcat2.close 
end sub

sub Showproducts(intCatID)

psql = ""
mypage=request.querystring("whichpage")
If  mypage="" then
   mypage=1
end if
mypagesize=request.querystring("pagesize")
If  mypagesize="" then
   mypagesize=12
end if
	
x=0
 set dbprod=CreateObject("ADODB.Recordset")
scriptname=request.servervariables("script_name")


	
	dbprod.cursorlocation=aduseclient
	dbprod.cachesize=5

      if isSortable=True then 
		  txtsql="select * from products3 where CategoryCode=" & intCatID & " order by sOrder asc"
	  else 
		  txtsql="select * from products3 where CategoryCode=" & intCatID 	
	  end if 
  	  'response.write txtsql
     	'response.end
	 
		dbprod.Open txtsql, dbconnect
			txtcat=getCategory(intCatID)
			   '------------------------------------------
			   if dbprod.eof then 
	
			%>
						<p class='pageHeading'>Products for Category  - <%=txtCat%></p>
					<table width="100%" border="0" align="center">
		      <tr valign="top">
		        <td> 
		        		<center><font Color="Red" size="3"> No Product Available for Category  - <%=txtcat%></font></center>
		        </td>
		      </tr>
		    </table>
		    <%
			else 
				Call ShowVendorPic(intCatID)
				%>
					<p class='pageHeading'>Products for Category  - <%=txtCat%></p>
				<%
			   '-----------------------------------------
				resultcnt=getResultCNT(intCatID)
			   dbprod.close

	dbprod.Open txtsql,dbconnect
	
	dbprod.movefirst
	dbprod.pagesize=mypagesize
	maxcount=cint(dbprod.pagecount)
	dbprod.absolutepage=mypage
	howmanyrecs=0
	howmanyfields=dbprod.fields.count -1

	%>
<table width="93%">
  <td>	
		<%       
			
response.write "<center>Page " & mypage & " of " & maxcount & " -    (" & resultcnt & ")  Total Found<center>"
response.write "<br>"
		response.write "<center>"
		'******************************************************
		if  maxcount > 1 then 
				pad="0"
				scriptname=request.servervariables("script_name")
					
				
				   ref1="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=1" 
				   ref1=ref1 & "&pagesize=" & mypagesize  & "'><<</a>"
				
				   response.write ref1 & "  "
				   'response.write "mypage=" & mypage & "<br>"
				   if clng(mypage) <=1 then 
					   mypage=1	
					   ref2="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & mypage
					   ref2=ref2 & "&pagesize=" & mypagesize & "'>Prev</a>"
				   else
					   	
					   ref2="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & mypage-1
					   ref2=ref2 & "&pagesize=" & mypagesize & "'>Prev</a>"
				   end if 	
				   response.write ref2 & " "
				
				   if mypage>=1 and maxcount = 1 then 
					mypage=1
				   end if
				   
				   '----------------------------	  
				
				  if maxcount > 1 then 
				  	for pcnt=1 to maxcount
				  		 pref="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & pcnt
				  		 pref=pref & "&pagesize=" & mypagesize & "'>" & pcnt & "</a>"
				  		response.write  pref & "  "
				  	next 
				  end if 
				  
				  '---------------------------
				   
				  	   	
				   if clng(mypage) >=clng(maxcount) then    
					   mypage=maxcount
					   ref3="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & mypage
					   ref3=ref3 & "&pagesize=" & mypagesize & "'>Next</a>"
				   else 
				   	   	
					   ref3="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & mypage+1
					   ref3=ref3 & "&pagesize=" & mypagesize & "'>Next</a>"
				   end if 	
				    
				   response.write ref3 & " "
				
				   ref4="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & maxcount
				   ref4=ref4 & "&pagesize=" & mypagesize & "'>>></a>"
				
					response.write ref4 & "  "
				
				
		'******************************************************
		end if 

		response.write "</center>"
		response.write "<br>"
			response.write "<table border=0 width=98% >"
			'response.write "<tr bgcolor=#8080C0><th>Picture</th><th>Description</th></tr>"
	intCnt=0
'response.write "howmanyrecs=" & howmanyrecs
'response.end
	do while not dbprod.eof and howmanyrecs < dbprod.pagesize
if dbprod("imagename") ="" then 
	txtImage=dbprod("url") 
else
	txtImage=dbprod("url") & dbprod("imagename")
end if 
			 	intUnitPrice=dbprod("unitprice")
			 	if prodcnt=2 then prodcnt=0
				 %>
				 	  <%
				 	    if prodcnt=0 then 
				 	  %>
			      <tr valign="top">
			      <%
			    		end if
					if intUnitPrice  > 0 or intUnitPrice <> "" then 
			    	%>
			        <td> 
			        		 <!---<img src="<%=txtImage%>" width="169" height="160">-->
			        		 	<!--BDDName,BDDSQL,BDDFieldName,BDDFieldValue,BDDsize,BDDalign,BDDSelected)-->
					<form action="/shopping/AddToCart.asp" method="post">
			        	<table width="100%" border="0">
			        			<tr>
			        				<%
			        				intID=dbprod("id")
			        				%>
			        					<!--<td><img src="<%=txtImage%>">width="169" height="160"></td>-->
			        					

			        					<!--<td height="264"><img src="<%=txtImage%>" width="169"></td>-->
			        					<td height="264"><a href="#" class='topNav' onclick="window.open('Gallery_Popup.asp?PID=<%=intID%>','cartwin','width=600,height=400,scrollbars,location,resizable,status');"> <span class="style2"><img src="<%=txtImage%>" width="169" border="0"></span></td>
			        			</tr>
			        			<tr>
			        				<td valign="top"><%=dbProd("ShortDescription") %></td>
			        			</tr>
			        			<tr>
			        					<%
			        						BDDName="ItemSize_" & dbProd("UPC") 
			        						'BDDSql="select * from sizes where categorycode=" & intCatID
			        						'response.write bddsql & "<BR>"
			        					'response.end
			        					%>
			        					
			        					<table width="100%" border="0">
			        				  <tr>
			        						<td>Size: </td><td>
			        							
			        							<%
			        							strItemSize=dbprod("itemsize")
			        							if strItemSize <> "" then 
				        							if instr(strItemSize, ",") = false then 
				        								%> <input type="text" value="<%=strItemSize%>" size="5" readonly> <%
				        					  	else 	
	
				        								call BuildDropDown(BDDName,strItemSize,"Sizes","Sizes","","Left")
				        							
				        						  end if 
				        						else 
				        							%> N/A <%
				        						end if 
			        							%>
			        							
			        							</td>
			        					</tr>
			        					<tr>
			        							<td>ItemNumber: </td><td><%=dbprod("ItemNumber")%></td>
			        					</tr>
			        					<tr>
			        						<% if intUnitPrice <> "" then %>
			        							<td>Price: </td><td>$<%=formatnumber(intUnitPrice,2,0,0)%></td>
			        						<%else%>
			        							<td colpan=2>&nbsp; </td>
			        						<%end if%>
			        					<tr>
			        			<tr>
			        	</table>
			        		<input size="5" type="hidden" name="Qty<%=dbProd("UPC")%>" Value="1">
			        		<input type="hidden" name="catid" value="<%=intCatid%>">
						<input type="submit" value="Add to Cart">
						</form>
			        </td>
			      
				 	  <%
					prodcnt=prodcnt + 1
					end if 
				 	    if prodcnt=2 then 
				 	  %>
			      </tr>
						<%
					end if 
					
		intcnt=intcnt+1			
		howmanyrecs=howmanyrecs+1
		dbprod.movenext

	loop
		


	%>
</table>
<%
response.write "<br>"

		response.write "<center>"
		'******************************************************
		if maxcount > 1 then 
				pad="0"
				scriptname=request.servervariables("script_name")
					
				
				   ref1="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=1" 
				   ref1=ref1 & "&pagesize=" & mypagesize  & "'><<</a>"
				
				   response.write ref1 & "  "
				   'response.write "mypage=" & mypage & "<br>"
				   if clng(mypage) <=1 then 
					   mypage=1	
					   ref2="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & mypage
					   ref2=ref2 & "&pagesize=" & mypagesize & "'>Prev</a>"
				   else
					   	
					   ref2="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & mypage-1
					   ref2=ref2 & "&pagesize=" & mypagesize & "'>Prev</a>"
				   end if 	
				   response.write ref2 & " "
				
				   if mypage>=1 and maxcount = 1 then 
					mypage=1
				   end if
				  
				  '----------------------------	  
				 
				  if maxcount > 1 then 
				  	for pcnt=1 to maxcount
				  		 pref="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & pcnt
				  		 pref=pref & "&pagesize=" & mypagesize & "'>" & pcnt & "</a>"
				  		response.write  pref & "  "
				  	next 
				  end if 
				  
				  '---------------------------
				  
				   if clng(mypage) >=clng(maxcount) then    
					   mypage=maxcount
					   ref3="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & mypage
					   ref3=ref3 & "&pagesize=" & mypagesize & "'>Next</a>"
				   else 
				   	   	
					   ref3="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & mypage+1
					   ref3=ref3 & "&pagesize=" & mypagesize & "'>Next</a>"
				   end if 	
				    
				   response.write ref3 & " "
				
				   ref4="<a href='" & scriptname & "?catid=" & intcatid & "&whichpage=" & maxcount
				   ref4=ref4 & "&pagesize=" & mypagesize & "'>>></a>"
				
					response.write ref4 & "  "
				
				
				'******************************************************
			end if 
		end if 

		response.write "</center>"

	dbprod.close
	set dbprod=nothing
%>
</td>
</table>
<%
end sub 

sub BuildDropDown(BDDName,BBDField, BDDFieldName,BDDFieldValue,BDDsize,BDDalign)
	if BDDalign="" then 
		BDDalign="Right"
	end if 
		'response.write BDDSelected & "<BR>"
	strBldfield=""
	%>
	<select name="<%=BDDNAME%>" size="<%=BDDsize%>" Align="<%=BDDAlign%>" class="claFormBox">
		<option value="">&lt;Not Selected&gt;</option>
			<%
			for x = 1 to len(BBDField)
						
			 if mid(BBDField,x,1 ) <> ","  then 
			 		 strBldTemp=mid(BBDField,x,1)
					 strBldfield=strBldfield & strBldTemp
					 strBldTemp = ""
			  end if 
			 if mid(BBDField,x,1)= "," or x=len(BBDField) then 
					%><option value="<%=strBldfield%>"><%=strBldfield%></option><%
					strBldfield=""
			 end if 

		next 
		%>	  
		
	</select>
	<%
end sub
	
%>





</body>
</html>