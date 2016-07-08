<?php
/**
 * Created by PhpStorm.
 * User: Chao
 * Date: 05/07/2016
 * Time: 3:52 PM
 */


function getMemberID($email){
    $con=mysqli_connect("localhost","root","","powon");
// Check connection
    if (mysqli_connect_errno())
        echo "Failed to connect to MySQL: " . mysqli_connect_error();

    $sql="SELECT * FROM member WHERE  emailaddress='$email'";
    if ($result=mysqli_query($con ,$sql))
    {
        // Fetch one and one row
        $row=mysqli_fetch_row($result);
        printf (" find matching member id: %s\n",$row[0]);
        // Free result set
        //mysqli_free_result($result);
    }
    //mysqli_close($con);
    return $row;
}

// need to change Group name to be unique
function createGroup($groupName, $ownerid, $groupInterest){
    $con=mysqli_connect("localhost","root","","powon");
// Check connection
    if (mysqli_connect_errno())
        echo "Failed to connect to MySQL: " . mysqli_connect_error();

    $sql="INSERT INTO `groups` (`groupid`, `ownerid`, `groupname`, `ginterest`, `createdate`) VALUES
                        ('', $ownerid, '$groupName', '$groupInterest', CURRENT_DATE());";
    if(mysqli_query($con ,$sql)){
        echo "group created successfully \n";
        $sql="SELECT * FROM groups WHERE  groupname='$groupName'";
        $result=mysqli_query($con ,$sql);
        $row=mysqli_fetch_row($result);
        $sql="INSERT INTO `joinin` (`groupid`, `powonid`, `joindate`) VALUES ($row[0],$ownerid, CURRENT_DATE());";
        mysqli_query($con ,$sql);
        return $row;
    }
    else{
        echo "group already exist";
    }
}

function addMemberToGroup($groupID, $powonID, $email, $firstName,$DOB){
    $con=mysqli_connect("localhost","root","","powon");
// Check connection
    if (mysqli_connect_errno())
        echo "Failed to connect to MySQL: " . mysqli_connect_error();

    $sql="SELECT * FROM `member` 
          WHERE `powonid`=$powonID AND `emailaddress`='$email' AND`firstname`='$firstName' AND`dob`='$DOB'";

    if ($result=mysqli_query($con ,$sql))
    {
        $row=mysqli_fetch_row($result);
        if($row == null) echo error;
        $mid =$row[0];
        $sql="INSERT INTO `powon`.`joinin` (`groupid`, `powonid`, `joindate`) VALUES ('$groupID', '$mid', CURRENT_DATE())";
        if(mysqli_query($con ,$sql)){
            echo "successfully added member id '$mid' to group id '$groupID'";
        }
        else{
            echo "member already in the group \n";
        }
    }
    else{
        echo "cannot find member \n";
    }
}

function getMemberGroup($powonID){
    $con=mysqli_connect("localhost","root","","powon");
// Check connection
    if (mysqli_connect_errno())
        echo "Failed to connect to MySQL: " . mysqli_connect_error();

    $sql = "SELECT groupname, ownerid
								FROM joinin, groups
								WHERE joinin.powonid='$powonID' AND joinin.groupid=groups.groupid";
    if ($result=mysqli_query($con ,$sql))
    {
        $groupList =array();
        while ($row = mysqli_fetch_assoc($result))
        {
            $groupName = $row['groupname'];
            $ownerId = $row['ownerid'];
            array_push($groupList, array($groupName, $ownerId));
        }

        return $groupList;
    }
    else{
        echo "this member does not belongs to any group \n";
    }
}

function getLatestGroupPost($powonID)
{
    $con = mysqli_connect("localhost", "root", "", "powon");
// Check connection
    if (mysqli_connect_errno())
        echo "Failed to connect to MySQL: " . mysqli_connect_error();

    $sql = "SELECT p1.*
            FROM post p1 LEFT JOIN post p2
            ON (p1.groupid = p2.groupid AND p1.createtime > p2.createtime) , joinin j
            WHERE p2.createtime IS NULL AND p1.groupid=j.groupid AND j.powonid=$powonID;  ";
    if ($result = mysqli_query($con, $sql)) {
        $postList = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $groupID = $row['groupid'];
            $postconent = $row['content'];
            array_push($postList, array($groupID, $postconent));
        }
        return $postList;
    } else {
        echo "this member does not belongs to any group \n";
    }
}
    $email='ddd@yahoo.com';
    $memberInforRow=getMemberID($email);

    $mid = $memberInforRow[0];
    $email = $memberInforRow[3];
    $firstName = $memberInforRow[5];
    $DOB = $memberInforRow[7];
    echo "find member $mid, his email is $email, his first name is $firstName, his DOB is $DOB  \n";

    $groupName='Game';
    $groupInterest='Assasin';
    $groupInfor=createGroup($groupName,$mid,$groupInterest);
    $gid=$groupInfor[0];
    echo "new created group id is $gid \n";

    addMemberToGroup(2, $mid, $email, $firstName,$DOB);

    $grouplist = array();
    $grouplist=getMemberGroup($mid);
    echo $grouplist[0][0],$grouplist[0][1], "\r\n";
    echo  $grouplist[1][0],$grouplist[1][1], "\r\n";
    echo  $grouplist[2][0],$grouplist[2][1], "\r\n";



    $postList = array();
    $postList=getLatestGroupPost($mid);
    echo $postList[0][0],$postList[0][1],"\r\n";
    echo $postList[1][0],$postList[1][1], "\r\n";

    echo "finish";
?>