<?php
include_once('../private/shared/header.php');
include_once('../private/initialize.php');

if (isset($_GET['action'])) { $action = $_GET['action'];} 
else { $action = 'home'; }

// Public Views

if ($action == 'home'){
    include('views/home.php');
}
else if ($action == 'voci'){
    if (isset($_SESSION['user_id']))
        include('views/voci/decks.php');
    else
        include('views/login.php'); 
}
else if ($action == 'docs'){
    include('views/docs.php'); 
}
else if ($action == 'login'){
    include('../private/database_queries/login.inc.php');
    if (isset($_SESSION['user_id'])){
        header('Location: index.php?action=voci');
    }
    else{
        header('Location: index.php?action=voci');
    }
}
else if ($action == 'logout'){
    include('../private/database_queries/logout.inc.php');
    header('Location: ?action=home');
}
else if ($action == 'register_view'){
    include('views/register.php'); 
}
else if ($action == 'register'){
    include('../private/database_queries/register.inc.php');
    header('Location: index.php?action=voci');
}

// User Views

// From Decks View

else if ($action == 'decks'){
    
    $_SESSION['user_id'];

    include('views/voci/decks.php'); 
}
else if ($action == 'edit_deck_view'){
    include('views/voci/edit_deck.php');
}
else if ($action == 'edit_deck'){
    include('../private/database_queries/edit_deck.inc.php');
    header('Location: index.php?action=decks'); 
}
else if ($action == 'delete_deck'){
    include('../private/database_queries/delete_deck.inc.php');
    header('Location: index.php?action=decks'); 
}
else if ($action == 'add_deck'){
    include('../private/database_queries/add_deck.inc.php');
    header('Location: index.php?action=decks');
}

// From Cards View

else if ($action == 'cards_view'){

    //If the user has chosen a new deck, we update the current deck id and deck name

    if (isset($_POST['deck_id']) && isset($_POST['deck_name'])){
        $_SESSION['deck_id'] = $_POST['deck_id'];
        $_SESSION['deck_name'] = $_POST['deck_name'];
    }

    $deck_id = $_SESSION['deck_id'];
    $query = "select * from cards where deck_id='$deck_id'";
    $cards = mysqli_query($connection, $query);

    include('views/voci/cards.php');
}
else if ($action == 'study_deck'){

    // If we have just initiated a study session, we must shuffle and setup the deck

    if(isset($_POST['initiate'])){
        include('../private/database_queries/study_deck.inc.php');
        
        // If we initiate a new study session, we must check if the deck is empty.

        if (isset($_SESSION['queue']) && count($_SESSION['queue']) != 0)
            header('Location: index.php?action=study_deck');
        else
            header('Location: index.php?action=cards_view');
    }   
    else if (($_SESSION['counter']) > 0){

        // Two cases: We want to see the front of the card

        if ($_SESSION['side'] == 0 && count($_SESSION['queue'])){

            // Flip the card over, and pop the next index

            $_SESSION['side'] = 1;
            $_SESSION['index'] = array_pop($_SESSION['queue']);

            // For each front facing card encountered, we set the front and back values
            // before it is displayed

            $_SESSION['front'] = $_SESSION['fronts'][$_SESSION['index']];
            $_SESSION['back'] = $_SESSION['backs'][$_SESSION['index']];
            include('views/voci/study_deck.php');
        }

        // Or the back.

        else {
            $_SESSION['side'] = 0;
            $_SESSION['counter']--;
            include('views/voci/study_deck.php');
        }
    }
    else
        header('Location: index.php?action=out_of_cards');
}
else if ($action == 'out_of_cards'){
    include('views/voci/out_of_cards.php');
}
else if ($action == 'add_card'){
    include('../private/database_queries/add_card.inc.php');
    header('Location: index.php?action=cards_view');
}
else if ($action == 'delete_card'){
    include('../private/database_queries/delete_card.inc.php');
    header('Location: index.php?action=cards_view');
}
?>