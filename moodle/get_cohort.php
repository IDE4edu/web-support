<?php  // $Id$

    /*
     * Put this in moodle1.9 /user directory, say, and hit it when logged
     * in as a teacher in a course that you specify with ?cid=<course_id>
     * 
     * Get back json that you can insert into bs_user table with XXX
     * fix teacherid in all records while you are at it.
     */
    
    require_once("../config.php");
    //require_once("../moodlelib.php");
    //require_once("../accesslib.php");
    //require_once("../grouplib.php");


    /// make sure they are a teacher in the right course...

    $cid = required_param('cid', PARAM_INT);  // course id

    
    if (! $course = get_record("course", "id", $cid)) {
        error("Wrong course id, yo");
    }

    if (!$context = get_context_instance(CONTEXT_COURSE, $cid)) {
        print_error('badcontext');
    }
    
    require_login($cid,false);
    if (!(has_capability('moodle/course:viewhiddenuserfields', $context))) {
        error("You don't have the right to do this!");
    }
    
    ///////
    
    $users = get_users_by_capability($context, 'mod/forum:viewdiscussion', 'u.id, u.firstname, u.lastname, u.idnumber', 'u.id ASC');
    //$groups = groups_get_all_groups($cid, "", "", "g.id, g.name");
    
    $eduride_users = array();
    foreach ($users as $u) {
        $eduride_user = array();
        $eduride_user['userId'] = $u->id;
        $eduride_user['userName'] = $u->firstname . " " . $u->lastname;
        $eduride_user['studentIdentifier'] = $u->idnumber;
        $groups = groups_get_all_groups($cid, $u->id);
        $group = array_pop($groups);
        $eduride_user['periodId'] = $group->id;
        $eduride_user['periodName'] = $group->name;
        $eduride_user['teacherId'] = "XXXX";
        $eduride_users[] = $eduride_user;
    }
    
    
    $json = json_encode($eduride_users);
    header('Content-Type: application/json');
    echo $json;
    
?>
    