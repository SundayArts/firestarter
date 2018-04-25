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