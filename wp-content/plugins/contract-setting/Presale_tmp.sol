pragma solidity ^0.4.15;

contract Owned {
    address public owner;   // owner address

    // event
    event TransferOwnership(address oldaddr, address newaddr);

    // modifier
    modifier onlyOwner() {require(msg.sender == owner);_;}

    // construct
    function Owned() public {
        owner = msg.sender;
    }

    // change owner
    function transferOwnership(address _new) public onlyOwner {
        address oldaddr = owner;
        owner = _new;
        TransferOwnership(oldaddr, owner);
    }
}

contract MyToken is Owned {
    string public name; // token name
    string public symbol; // token unit
    uint8 public decimals; // token decimals
    uint public totalSupply; // total token
    bool private stopped = false; // CircuitBreaker flag
    mapping(address => uint) public balanceOf; // Token holding amount with respect to address
    mapping(address => int8) public blackList; // black list
    mapping(address => mapping(address => uint)) public allowed; // Allow the user to handle tokens paid out from the owner for distribution

    mapping(address => bool) private Authenticated;
    uint private lockupPeriod;
    bool private isLockup;

    event Transfer(address indexed _from, address indexed _to, uint _value);
    event TransferFrom(address indexed _from, address indexed _to, uint _value);
    event Approval(address indexed _owner, address indexed _spender, uint _value);
    event Blacklisted(address indexed target);
    event DeleteFromBlacklist(address indexed target);
    event RejectedPaymentToBlacklistedAddr(address indexed from, address indexed to, uint value);
    event RejectedPaymentFromBlacklistedAddr(address indexed from, address indexed to, uint value);
    event Burn(address indexed from, uint value);
    event ToggleCircuit(bool flag);

    modifier isStopped() {require(!stopped);_;}

    // construct
    function MyToken() public {
        totalSupply = convert2square(@total_supply@);
        balanceOf[msg.sender] = totalSupply;
        name = "@token_name@";
        symbol = "@symbol@";
        decimals = @decimals@;
        isLockup = @isLockup@;
        lockupPeriod = @lockupPeriod@;
    }

    // Apply a _value minute token to _to and subtract it from the total amount of tokens possessed by the owner
    function transfer(address _to, uint _value) public isStopped returns(bool) {
        if (isLockup) {
          require(now > lockupPeriod || Authenticated[msg.sender] || msg.sender == owner);
        }
        require(balanceOf[msg.sender] >= _value);
        require((balanceOf[_to] + _value) >= balanceOf[_to]);

        if (blackList[msg.sender] > 0) {
            RejectedPaymentFromBlacklistedAddr(msg.sender, _to, _value);
        } else if (blackList[_to] > 0) {
            RejectedPaymentToBlacklistedAddr(msg.sender, _to, _value);
        } else {
            balanceOf[msg.sender] -= _value;
            balanceOf[_to] += _value;
            Transfer(msg.sender, _to, _value);
        }
    }

    // Function for distribution after token acquisition
    function transferFrom(address _from, address _to, uint _value) public isStopped returns(bool) {
        require(balanceOf[_from] >= _value && allowed[_from][msg.sender] >= _value && _value > 0);
        balanceOf[_from] -= _value;
        balanceOf[_to] += _value;
        allowed[_from][msg.sender] -= _value;
        TransferFrom(_from, _to, _value);
        return true;
    }

    // Grant distribution authority to holders obtained from publisher (safety measures not permitting other distribution)
    function approve(address _spender, uint _value) public isStopped returns(bool) {
        allowed[msg.sender][_spender] = _value;
        Approval(msg.sender, _spender, _value);
        return true;
    }

    // 
    function allowance(address _owner, address _spender) public constant returns(uint) {
        return allowed[_owner][_spender];
    }

    // Capital increase of token ※ Deposit to owner
    function mintToken(uint _mintedAmount) public onlyOwner isStopped {
        uint cnvAmount = convert2square(_mintedAmount);
        balanceOf[msg.sender] += cnvAmount;
        totalSupply += cnvAmount;
        Transfer(0, owner, cnvAmount);
    }

    // Switch circuit breaker flag
    function toggleCircuit(bool _stopped) public onlyOwner {
        stopped = _stopped;
        ToggleCircuit(_stopped);
    }

    // Register the address in the blacklist
    function blacklisting(address _addr) public onlyOwner isStopped {
        blackList[_addr] = 1;
        Blacklisted(_addr);
    }

    // Delete address from blacklist
    function deleteFromBlacklist(address _addr) public onlyOwner isStopped {
        blackList[_addr] = -1;
        DeleteFromBlacklist(_addr);
    }

    // Incineration of tokens possessed by the owner ※ Since the balance of the owner's token is reduced,
    function burn(uint _value) public onlyOwner isStopped {
        uint cnvAmount = convert2square(_value);
        require(balanceOf[msg.sender] >= cnvAmount);
        balanceOf[msg.sender] -= cnvAmount;
        totalSupply -= cnvAmount;
        Burn(msg.sender, cnvAmount);
    }

    // 10 to the Exponentiation of decimals
    function convert2square(uint _value) private constant returns(uint) {
        return _value * 10 ** uint(decimals);
    }

    // 10 to the power of decimals
    function convert2power(uint _value) private constant returns(uint) {
        return _value / 10 ** uint(decimals);
    }

    function changePermissionAddress(address _address, bool _value) public onlyOwner {
      Authenticated[_address] = _value;
    }

    //Discard Contract * When executed, all issue tokens are deleted. Be careful as it can not be restored.
    function selfDestruct() public onlyOwner {
        selfdestruct(owner);
    }

}

contract Presale is Owned {
    uint public fundingGoal; // Goal ether
    uint public startTime; // Start time
    uint public deadline; // End time
    uint public price; // The amount of tokens for 1 ether
    uint public transferableToken; // Number of tokens available for sale
    uint public soldToken; // Number of tokens sold

    bool public fundingGoalReached; // Goal attainment flag
    bool public isOpened; // Cloud sale holding determination flag
    bool private stopped = false;// Circuit breaker flag

    mapping(address => Property) public fundersProperty; // Payment information for address
    MyToken public tokenReward; // MyToken's address

    address[] private addressList;
    uint private index;
    mapping(address => uint) private indexAddress;

    // setting items
    uint private feePer = @fee_per@;
    address private feeAddress = @recieve_fee_address_private@;
    bool private isDonate = @isDonate@;

    struct Property {
        uint paymentEther; // payment ether
        uint reservedToken; // reserved token
        bool withdrawed; // [Withdrawed:true]、[Not withdrawn:false]
    }

    event CrowdsaleStart(uint _fundingGoal, uint _deadline, uint _transferableToken, address _beneficiary);
    event ReservedToken(address _backer, uint _amount, uint _token);
    event CheckGoalReached(address _beneficiary, uint _fundingGoal, uint _amountRaised, bool _reached, uint _raisedToken);
    event WithdrawalToken(address _addr, uint _amount, bool _result);
    event WithdrawalEther(address _addr, uint _amount, bool _result);
    event SendFee(bool success,uint fee);
    event ToggleCircuit(bool flag);
    event Continue(address _user);

    modifier afterDeadline() {require(now >= deadline);_;}
    modifier isStopped() {require(!stopped);_;}

    // construct
    function Presale() public {
        tokenReward = MyToken(@token_address@);
        fundingGoal = @pre_funding_goal@ * 1 ether;
        price = 1 ether / @pre_price@;
        transferableToken = @pre_transferable_token@ * (10 ** uint(tokenReward.decimals()));
    }

    // Cloud sale start method (4 weeks and 5 minutes from specified date)
    // * When using this method, pay attention to the time lag with the live network (9 hours in Japan)
    function startDate() public onlyOwner isStopped {
        require(fundingGoal != 0 && price != 0 && transferableToken != 0 && tokenReward != address(0) && startTime == 0);
        if (tokenReward.balanceOf(this) >= transferableToken) {
            startTime = @pre_start_time@;
            deadline = @pre_end_time@;
            CrowdsaleStart(fundingGoal, deadline, transferableToken, owner);
        }
    }

    // Anonymous function (Used arbitrarily when ether is transferred)
    function () public payable isStopped {
        if (now >= startTime && now <= deadline) {
            isOpened = true;
        }

        require(tokenReward.blackList(msg.sender) <= 0);
        require(isOpened && now < deadline);

        uint amount = msg.value;
        uint token = amount * (10 ** uint(tokenReward.decimals())) / price;
        require(token != 0 && (soldToken + token) <= transferableToken);

        if (fundersProperty[msg.sender].paymentEther == 0) {
            addressList.push(msg.sender);
            indexAddress[msg.sender] = index;
            index++;
        }

        fundersProperty[msg.sender].paymentEther += amount;
        fundersProperty[msg.sender].reservedToken += token;
        soldToken += token;
        ReservedToken(msg.sender, amount, token);
    }

    // You can check the time to finish (minutes), the number of till the target ehter, and the remaining amount of the token
    function getRemainingTimeEthToken() public constant returns(uint min, uint shortage, uint remainToken) {
        if (now < deadline) {
            min = (deadline - now) / (1 minutes);
        }
        if ((fundingGoal - this.balance) / (1 ether) > fundingGoal) {
            shortage = 0;
        } else {
            shortage = (fundingGoal - this.balance) / (1 ether);
        }
        remainToken = transferableToken - soldToken;
    }

    // Method to be done after Cloud Sale is over
    function checkGoalReached() public afterDeadline isStopped {
        if (isOpened) {
            if (this.balance >= fundingGoal) {
                fundingGoalReached = true;
            }
            isOpened = false;
            CheckGoalReached(owner, fundingGoal, this.balance, fundingGoalReached, soldToken);
        }
    }

    // Drawer method for fund providers (executable after sale ends)
    function withdrawal() public onlyOwner isStopped afterDeadline {
        require(!isOpened);

        for (uint i = 0; i < index; i++) {
            address _address = addressList[i];
            // Throw an exception if you are a blacklisted user
            if (
                tokenReward.blackList(_address) < 0 || 
                fundersProperty[_address].withdrawed || 
                tokenReward.balanceOf(this) < fundersProperty[_address].reservedToken || 
                tokenReward.balanceOf(this) - fundersProperty[_address].reservedToken > tokenReward.balanceOf(this)
            ) {
                Continue(_address);
                continue;
            }

            // send [Achievement：token] [Not Achievement：ether]
            if (fundingGoalReached) {
                if (fundersProperty[_address].reservedToken > 0) {
                    fundersProperty[_address].withdrawed = true;
                    tokenReward.transfer(_address, fundersProperty[_address].reservedToken);
                    WithdrawalToken(_address, fundersProperty[_address].reservedToken, fundersProperty[_address].withdrawed);
                }
            } else {
                if (fundersProperty[_address].paymentEther > 0) {
                    if (_address.call.value(fundersProperty[_address].paymentEther)()) {
                        fundersProperty[_address].withdrawed = true;
                    }
                    WithdrawalEther(_address, fundersProperty[_address].paymentEther, fundersProperty[_address].withdrawed);
                }
            }
        }

        // send [Achievement：Excess token,ether] [Not Achievement：token]
        if (fundingGoalReached) {
            // ether
            uint amount = this.balance;
            if (amount > 0) {

                // Donate
                if(isDonate) {
                    sendFee(amount);
                    amount = this.balance;
                }

                bool ok = msg.sender.call.value(amount)();
                WithdrawalEther(msg.sender, amount, ok);
            }
            // Excess Token
            uint val = transferableToken - soldToken;
            if (val > 0) {
                tokenReward.transfer(msg.sender, val);
                WithdrawalToken(msg.sender, val, true);
            }
        } else {
            // token
            uint val2 = tokenReward.balanceOf(this);
            tokenReward.transfer(msg.sender, val2);
            WithdrawalToken(msg.sender, val2, true);
        }
    }

    // toggle CircuitBreaker
    function toggleCircuit(bool _stopped) public onlyOwner {
        stopped = _stopped;
        ToggleCircuit(_stopped);
    }

    // CAUTION: This method destroys the contract and sends ether to the owner
    function selfDestruct() public onlyOwner {
        tokenReward.transfer(owner, tokenReward.balanceOf(owner));
        selfdestruct(owner);
    }

    function sendFee(uint amount) private returns(bool) {
        uint fee = amount * feePer / 100;
        uint fee2 = fee / 1 ether * 1 ether;
        bool success;

        if (fee - fee2 != 0 && fee > 1 ether) {
            fee2 += 1 ether;
            success = feeAddress.call.value(fee2)();
            SendFee(success,fee2);
            return success;
        } else {
            success = feeAddress.call.value(fee)();
            SendFee(success,fee);
            return success;
        }
    }
}