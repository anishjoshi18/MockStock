<?php
class User
{
    var $id;
    var $name;
    var $balance;
    
    function __construct($new_id, $conn)
    {
        $this->id = $new_id;
        $this->get_name($conn);
        $this->get_balance($conn);
    }
    
    function set_balance($conn, $new_balance)
    {
        $this->balance = $new_balance;
        
        //also need to update in the database
        $query = "UPDATE users SET balance = $new_balance WHERE id = $this->id";
        
        if(mysqli_query($conn, $query))
        {
            
        }
        else
            echo "Error balance update in db";
    }
        
    function get_id()
    {
        return $this->id;
    }
    
    function get_name($conn)
    {
        $query = "SELECT first_name FROM users WHERE id='$this->id'";
        if($run = mysqli_query($conn, $query))
        {
            while($array = mysqli_fetch_assoc($run))
            {
                $name = $array['first_name'];
                $this->name = $name;
            }
            return $name;
        }
    }
    
    function get_balance($conn)
    {
        $query = "SELECT balance FROM users WHERE id='$this->id'";
        if($run = mysqli_query($conn, $query))
        {
            while($array = mysqli_fetch_assoc($run))
            {
                $balance = $array['balance'];
                $this->balance = $balance;
            }
            return floatval($balance);
        }
    }
    
    //for updating the user's basic info
    function set_basic_info($conn, $first_name, $last_name, $email)
    {
        $query_change_info = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', email = '$email' WHERE id = '$this->id'";
        
        if(mysqli_query($conn, $query_change_info))
            return true;
    }
        
    //function to place the order
    function placeOrder($conn, $type, $company_id, $quantity, $limit_or_market, $limit_price)
    {       
        //first check if this is a valid order to be placed
        
        //check if has enough balance to buy
        if($type == "buy")
        {
            //first get price of the share
            $company = new Company($company_id);
            $price = floatval($company->get_company_price($conn));

            if($this->balance < $quantity*$price)
            {
                echo "<script>alert('You do not have enough balance to place this order'); </script>";
                return false;
            }
        }
        elseif($type == "sell")
        {
            //check if has enough shares to sell

            if($quantity > $this->get_user_quantity($conn, $company_id))
            {
                echo "<script>alert('You do not own enough shares to place this order'); </script>";
                return false;
            }
        
        }

        $query = "INSERT INTO orders(user_id, company_id, quantity, type, limit_or_market, limit_price) VALUES('$this->id','$company_id','$quantity','$type','$limit_or_market','$limit_price')";

        if(mysqli_query($conn, $query))
        {
            return true;
        }
        else
            echo "Error placing the order";
    }
    
    //get the quantity of shares for a company
    function get_user_quantity($conn, $company_id)
    {
        $query = "SELECT quantity FROM shares WHERE user_id = '$this->id' AND company_id = '$company_id'";
        if($run = mysqli_query($conn, $query))
        {
            if(mysqli_num_rows($run) < 1)
            {
                return 0;
            }
            while($array = mysqli_fetch_assoc($run))
            {
                $quantity = $array['quantity'];
            }
            
            return $quantity;
            
        }
    }
      
    //execute the orders for this user
    function executeOrders($conn)
    {
        $message_to_return = "";
        //check with all the orders in the table orders
        $query_get_orders = "SELECT * FROM orders WHERE user_id = '$this->id'";
        if($run_get_orders = mysqli_query($conn, $query_get_orders))
        {
            if(mysqli_num_rows($run_get_orders) < 1)
            {
                return $message_to_return;
            }
            $count = 0;
            //for eah order
            while($array = mysqli_fetch_assoc($run_get_orders))
            {
                $order_id = $array['id'];
                $company_id = $array['company_id'];
                $type = $array['type'];
                $quantity = $array['quantity'];
                $limit_or_market = $array['limit_or_market'];
                $limit_price = floatval($array['limit_price']);
                
                //get price of the share
                $company = new Company($company_id);
                $price = floatval($company->get_company_price($conn));
                
                //check validity for limit orders
                if($limit_or_market == "limit" && (($type == "sell" && $price  < $limit_price) || ($type == "buy" && $price >$limit_price)))    
                {
                    continue;
                }
                
                $total_price = $quantity*$price;
                
                if($type == "buy")
                {
                    $this->balance -= $total_price;
                    if($this->balance < 0)
                    {
                        $this->balance+=$total_price;
                        $message_to_return.="One order could not be executed due to: Insufficient Balance.";
                        continue;
                    }
                
                }
                
                if($type == "sell")
                {
                    $this->balance += $total_price;
                }
                
                
                //insert into or update shares table
                
                    //check if some shares already there
                    $query = "SELECT * FROM shares WHERE company_id = '$company_id' AND user_id = '$this->id'"; 
                
                    if($run = mysqli_query($conn, $query))
                    {
                    
                        if(mysqli_num_rows($run) == 1)
                        {
                            
                            while($array = mysqli_fetch_assoc($run))
                            {
                                $owned = $array['quantity'];
                            }
                            //update shares quantity
                            if($type == "buy")
                                $query_update = "UPDATE shares SET quantity = quantity + '$quantity' WHERE company_id = '$company_id' AND user_id = '$this->id'";
                            elseif($type == "sell")
                            {
                                if($owned < $quantity)
                                {
                                    $this->balance -= $total_price;
                                    $message_to_return.="One order could not be executed due to: Insufficient Shares<br>";
                                    continue;
                                }
                                $new_quantity = $owned - $quantity;
                                if($new_quantity > 0)
                                    $query_update = "UPDATE shares SET quantity = '$new_quantity' WHERE company_id = '$company_id' AND user_id = '$this->id'";
                                elseif($new_quantity == 0)
                                    $query_update = "DELETE FROM shares WHERE company_id = $company_id AND user_id = $this->id";
                            }

                            if(mysqli_query($conn, $query_update))
                            {
                                
                            }
                            else
                                echo "Error updating shares";
                        }
                        elseif(mysqli_num_rows($run) == 0)
                        {
                            
                            if($type == "sell")
                            {
                               $this->balance -= $total_price;
                               $message_to_return.="One order could not be executed due to: Insufficient Shares<br>";
                               continue;
                            }

                            //insert new entry
                            if($type == "buy")
                            {
                                $query = "INSERT INTO shares(user_id, company_id, quantity) VALUES ('$this->id', '$company_id', '$quantity')";

                                if(mysqli_query($conn, $query))
                                {
                                    
                                }
                                else
                                    echo "Couldnt insert shares";
                            }
                        }
                  
                    }                
                //update user balance
                $this->set_balance($conn, $this->balance);
                
                //insert into transactions table
                if($type == "buy")
                    $query = "INSERT INTO transactions(user_id, company_id, quantity, price) VALUES ('$this->id', '$company_id', '$quantity', '$price')";
                elseif($type == "sell")
                    $query = "INSERT INTO transactions(user_id, company_id, quantity, price) VALUES ('$this->id', '$company_id', '-$quantity', '$price')";
                if(mysqli_query($conn, $query))
                {
                    
                }
                else
                    echo "Error transaction add";
                
                
                //now delete from the orders table
                $query_delete = "DELETE FROM orders WHERE id = '$order_id'";
                
                if(mysqli_query($conn, $query_delete))
                {
                   
                }
                else
                {
                    echo "Error deleting order";
                }
                $count++;
                if($count == 1)
                    $message_to_return .= "One Order was executed. See <a href='trades.php'>Trade Book</a>";
                elseif($count > 1)
                    $message_to_return .= "$count Orders were executed. See <a href='trades.php'>Trade Book</a>";
            }
            return $message_to_return;
        }
    }
    
    //check for notifications (messages) for user
    function checkMessages($conn)
    {
        $query = "SELECT message FROM users WHERE id = $this->id";
        if($run = mysqli_query($conn, $query))
        {
            while($array = mysqli_fetch_assoc($run))
            {
                $message = $array['message'];
                
                if($message != "")
                {
                    echo "<div id='note'>$message<a id='close' class='pull-right'>[Close]</a></div>";
                    
                    //this is for removing the message
                    $message = "";
                }             
                
                 $query_message = "UPDATE users SET message = '$message' WHERE id = '$this->id'";
        
                 mysqli_query($conn, $query_message);
            }
        }
    }
    
    //restart the game, delete/reset things
    function restartGame($conn)
    {
        //delete all the things done by user, set user's balance to 500000
        $query_all = "DELETE FROM shares WHERE user_id = $this->id; DELETE FROM transactions WHERE user_id = $this->id; DELETE FROM orders WHERE user_id = $this->id; UPDATE users SET balance = 500000, message='Reset Successfull.', highest_rank = '500' WHERE id = $this->id";
        
        if(mysqli_multi_query($conn, $query_all))
        {
            $this->balance = 500000;
            header("Location:index.php");
        }
        else
           echo mysqli_error($conn);
        
        return;
    }
    
    //returns valuation of all the shares
    function get_valuation($conn)
    {
        $valuation_in_shares = 0;
        $query_shares = "SELECT * FROM shares WHERE user_id = $this->id";
                                
        if($run_shares = mysqli_query($conn, $query_shares))
        {
            if(mysqli_num_rows($run_shares) < 1)
            {
                $valuation_in_shares = 0;
            }
            else
            {
                //for each company
                while($array_shares = mysqli_fetch_assoc($run_shares))
                {
                    $company_id = $array_shares['company_id'];
                    $quantity = $array_shares['quantity'];
                                                        
                    $query_company_price = "SELECT price FROM companies WHERE id = $company_id";
                                                        
                    $getPrice = mysqli_fetch_assoc(mysqli_query($conn, $query_company_price));
                    $company_price = $getPrice['price'];
                                                        
                    $shares_value = $quantity*$company_price;
                                                        
                    $valuation_in_shares += $shares_value;
                                                        
                }
                                                    
            }
        } 
        return $valuation_in_shares;
    }
    
    //change user's consent about showing info of his stocks to others
    function set_consent($conn, $consent)
    {
        if(mysqli_query($conn, "UPDATE users SET consent = '$consent' WHERE id = '$this->id'"))
            return true;
        else
            return false;
    }
    
}


class Company
{
    var $id;
    var $name;
    var $price;
    
    function __construct($new_id)
    {
        $this->id = $new_id;
    }
    

    //used only when the admin changes the price
    function set_price($conn, $new_price)
    {
        $this->price = $new_price;
        
        $query = "SELECT price, high, low FROM companies WHERE id = $this->id";
        if($run = mysqli_query($conn, $query))
        {
            while($array = mysqli_fetch_assoc($run))
            {
                $price = $array['price'];
                $high = $array['high'];
                $low = $array['low'];

                if($new_price > $high)
                    $high = $new_price;
                elseif($new_price < $low)
                    $low = $new_price;

                $query_update = "UPDATE companies SET price = $new_price, prev_price = $price, high = $high, low = $low WHERE id = $this->id";

                if(mysqli_query($conn, $query_update))
                {

                }
                else
                    echo "Err";

            }
        }
        
        //insert into 'price_variation' table
        $query_price = "INSERT INTO price_variation(company_id, price) VALUES('$this->id', '$new_price')";
        if(mysqli_query($conn, $query_price))
        {		

        }
        else
            echo "Error price table";
        
        return true;
    }
    
    function get_company_name($conn)
    {   
        $query = "SELECT name FROM companies WHERE id = $this->id";

        if($run = mysqli_query($conn, $query))
        {
            while($array = mysqli_fetch_assoc($run))
            {
                $name = $array['name'];
            }

            return $name;
        }
    }
    
     //get company price from its id
    function get_company_price($conn)
    {
        $query = "SELECT price FROM companies WHERE id='$this->id'";
        if($run = mysqli_query($conn, $query))
        {
            while($array = mysqli_fetch_assoc($run))
            {
                $price = $array['price'];
            }
            return floatval($price);
        }
    }
    
     //get low price from its id
    function get_low_price($conn)
    {
        $query = "SELECT low FROM companies WHERE id='$this->id'";
        if($run = mysqli_query($conn, $query))
        {
            while($array = mysqli_fetch_assoc($run))
            {
                $low = $array['low'];
            }
            return $low;
        }
    }
    
     //get high price from its id
    function get_high_price($conn)
    {
        $query = "SELECT high FROM companies WHERE id='$this->id'";
        if($run = mysqli_query($conn, $query))
        {
            while($array = mysqli_fetch_assoc($run))
            {
                $high = $array['high'];
            }
            return $high;
        }
    }
    
     //get high price from its id
    function get_abbr($conn)
    {
        $query = "SELECT abbr FROM companies WHERE id='$this->id'";
        if($run = mysqli_query($conn, $query))
        {
            while($array = mysqli_fetch_assoc($run))
            {
                $abbr = $array['abbr'];
            }
            return $abbr;
        }
    }
    
}

class News
{
    var $id;
    var $name;
    var $description;
    
    function __construct($new_id)
    {
        $this->id = $new_id;
    }
    

    function get_news_name($conn)
    {   
        $query = "SELECT name FROM news WHERE id = $this->id";

        if($run = mysqli_query($conn, $query))
        {
            while($array = mysqli_fetch_assoc($run))
            {
                $name = $array['name'];
            }

            return $name;
        }
    }
}


class Order
{
    var $id;
    var $user_id;
    var $company_id;
    var $quantity;
    var $type;
    var $limit_price;
    
    function __construct($new_id)
    {
        $this->id = $new_id;
    }
    
    function get_id()
    {
        return $this->id;
    }
    
    function get_company_id()
    {
        return $this->company_id;
    }
    
    function get_user_id()
    {
        return $this->user_id;
    }
    
    function get_limit_price()
    {
        return $this->limit_price;
    }
    
    function delete_order($conn)
    {
        $query_delete = "DELETE FROM orders WHERE id=$this->id";
        
        if(mysqli_query($conn, $query_delete))
        {
            return true;
        }
        else
            return false;
    }
    
    function edit_order($conn, $new_price, $new_quantity)
    {
        $query_update = "UPDATE orders SET limit_price = '$new_price', quantity = $new_quantity WHERE id=$this->id";
        
        if(mysqli_query($conn, $query_update))
        {
            return true;
        }
        else
            return false;
    }
    
    
}


class Transaction
{
    var $company_id;
    var $id;
    var $user_id;
    var $quantity;
    var $price;
    
    function __construct($new_id, $new_user_id, $new_company_id, $new_quantity, $new_type, $new_price)
    {
        $this->id = $new_id;
        $this->company_id = $new_company_id;
        $this->user_id = $new_user_id;
        $this->type = $new_type;
        $this->price = $new_price;
        $this->quantity = $new_quantity;
    }
    
    function get_id()
    {
        echo $this->id;
    }
    
    function get_company_id()
    {
        echo $this->company_id;
    }
    
    function get_user_id()
    {
        echo $this->user_id;
    }
    
    function getprice()
    {
        echo $this->price;
    }
    function get_quantity()
    {
        echo $this->quantity;
    }
}
?>