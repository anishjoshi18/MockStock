<?php
function changePrices($conn,$incdec,$sl)
{ 
    $query = "SELECT id, time, price, high, low FROM companies";
    if($run = mysqli_query($conn, $query))
    {
        while($array = mysqli_fetch_assoc($run))
        {
            $time = $array['time'];
            
            if($time > time())
            {
                continue;
            }
            
            $company_id = $array['id'];
            $price = $array['price'];
            $high = $array['high'];
            $low = $array['low'];
            
            
            if($incdec=='i')
			{
				if($sl=='s'){
					$rand_price = random_float(10, 30);	
				}
				else{
					$rand_price = random_float(30, 50);
				}
			}
			else
			{
				if($sl=='s'){
					$rand_price = random_float(-30, -10);	
				}
				else{
					$rand_price = random_float(-50, -30);
				}
			}
            $new_price = round($price + $rand_price, 1);
            if($new_price < 0)
            {
                $new_price += round(abs(2*$rand_price), 1);
            }
            
            if($new_price > $high)
                $high = $new_price;
            elseif($new_price < $low)
                $low = $new_price;
            
            $query_update = "UPDATE companies SET price = $new_price, time = $time, prev_price = $price, high = $high, low = $low WHERE id = $company_id";
            
            if(mysqli_query($conn, $query_update))
            {
                //insert into 'price_variation' table
                $query_price = "INSERT INTO price_variation(company_id, price) VALUES('$company_id', '$new_price')";
                if(mysqli_query($conn, $query_price))
                {		

                }
                else
                    echo "Error price table";
            }
            else
                echo "Err";
            
        }
    }
}
?>