<?php

/**
 * henry
 *
 * PHP service wrapper for Henry Turnstiles : https://www.henry.com.br/
 *
 * @author  Rogério Albandes <rogerio.albandes@gmail.com>
 * @version 0.1
 * @package henry
 * @example example.php
 * @link    https://github.com/albandes/henry
 * @license GNU License
 *
 */
 

class henry
{

    /*
     *  Maximum message size
     */
    const MESSAGE_MAX_LEN = '2048';

    /**
     * Turnstile ipv4 address
     *
     * @var string
     */
    private $_ip;

    /**
     * Turnstile connection port
     *
     * @var string
     */
    private $_port;

    /**
     * Turnstile´s  socket 
     *
     * @var null
     */
    private $_socket = null;
    
        
    public function __construct ($ip = '192.168.1.1', $port='3000')
    {
        $this->_ip   = $ip;
        $this->_port = $port;
    }
    
    function display()
    {
        echo $this->_port . "<br />";    
        echo $this->_ip . "<br />";
    }    

     /**
     * Set Turnstile ipv4 address
     *
     * @param string $ip Turnstile ipv4 address
     *
     * @return void
     */
    public function setIp ($ip)
    {
        $this->_ip = $ip;
    }
    
    /**
     * Set Turnstile port
     *
     * @param string $port Turnstile connection port
     *
     * @return void
     */
    public function setPort ($port)
    {
        $this->_port = $port;
    }

    /**
     * Connect to equipment
     * Creates the socket
     *
     * @return string|true Error message or true if connect
     *
     */
    public function connect()
    {
        if( !empty($this->_socket) )
            return " Socket exists ."; // << impede a recriação da conexão caso ela já exista

        $this->_socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if( empty($this->_socket) ){
            return "socket_create() falhou: ".socket_strerror(socket_last_error());
        }
        @socket_set_option($this->_socket, SOL_SOCKET, SO_RCVTIMEO, array('sec'=>30,'usec'=>500));
        $result = @socket_connect($this->_socket, $this->_ip, $this->_port);
        if( empty($result) ){
            @socket_close($this->_socket);
            $this->_socket = false;
            return "socket_connect() fail, message: ".socket_strerror(socket_last_error());
        }
        return true;
    }

    /**
     *  Writes to socket from the given buffer
     *
     * @param string $command Command to be sent to turnstile
     *
     * @return string|true Error message or true if socket was writed
     *
     */
    public function writeSocket($command)
    {
        $response = @socket_write($this->_socket, $command, strlen($command));

        if( $response === false ){
            $this->_socket = null;
            return "socket_write() fail, message: ".socket_strerror(socket_last_error());
        }

        return true;
    }

    /**
     *  Listen Turnstile
     *
     *  @author Júlio Filho
     *  @author Rogério Albandes <rogerio.albandes@gmail.com>
     *
     *  @return array [
     *                 'success'                    => true|false,
     *                 'message'                    => Error or success message
     *                 'original_response'          => Unchanged string that the turnstile sent
     *                 'original_response_bytes'    => Unchanged string that the ratchet sent , converted in bytes array
     *                 'size'                       => Size of received data
     *                 'index'                      => message index / thread number
     *                 'command'                    => Command identifier ex: ED, REON, EMSG, etc
     *                 'err_or_version'             => Error code (response) or message version (sending)
     *                 'data'                       => Message, if reply, usually separated by "]". Read the documentation
     *                ]
    */
    public function listen()
    {
        $this->connect();
        //if( $erro ) return $erro;
        $arrayReturn = array();
        $response = @socket_read($this->_socket, self::MESSAGE_MAX_LEN);

        if( empty($response) ){
            $error = socket_last_error();
            @socket_close($this->_socket);
            $this->_socket = null; // em caso de falha, força a reconexão
            if( $error == 11 ){
                $arrayReturn['success'] = false;
                $arrayReturn['message'] = 'Timeout';
                return $arrayReturn;
            }
            else{
                $arrayReturn['success'] = false;
                $arrayReturn['message'] = "socket_read() falhou: ".socket_strerror($error);

                return $arrayReturn;
            }
        }


        $bytes = unpack('C*', $response);
        $length = count($bytes);
        if( empty($bytes[1]) || $bytes[1] !== 2 ){
            $arrayReturn['success'] = false;
            $arrayReturn['message'] = 'a - interferência na comunicação com o equipamento';
            return $arrayReturn;

        }

        if( empty($bytes[$length]) || $bytes[$length] !== 3 ){
            $arrayReturn['success'] = false;
            $arrayReturn['message'] = 'b - interferência na comunicação com o equipamento';
            return $arrayReturn;
        }

        $arrayReturn['success'] = true;
        $arrayReturn['message'] = 'Success';
        $arrayReturn['original_response'] = $response;
        $arrayReturn['original_response_bytes'] = $bytes;
        $arrayReturn['size'] = $bytes[2];
        $arrayReturn['index'] = chr($bytes[4]).chr($bytes[5]);
        $arrayReturn['command'] = "";
        for($i=7; $bytes[$i]!=43 && $i<$length-1; $i++){
            $arrayReturn['command'] .= chr($bytes[$i]);
        }
        $arrayReturn['err_or_version'] = "";
        for($i=$i+1; $bytes[$i]!=43 && $i<$length-1; $i++){
            $arrayReturn['err_or_version'] .= chr($bytes[$i]);
        }
        $arrayReturn['data'] = '';
        for($i=$i+1; $i<$length-1; $i++){
            $arrayReturn['data'] .= chr($bytes[$i]);
        }
        return $arrayReturn;

    }

    function generate($sString) {
        if(!empty($sString)) {
            $sByteInicial = "02 ";
            $sTamanhoMensagem = $this->getStringSize($sString);
            $sMensagem = $this->string2Hex($sString);
            $sCheckSun = $this->getCheckSum($sString);
            $sByteFinal = " 03";

            return $sByteInicial . $sTamanhoMensagem . $sMensagem . $sCheckSun . $sByteFinal;
        } else
            return false;
    }

    function getStringSize($sString) {
        $nTamanhoString = strlen($sString);
        $nHex1 = $nTamanhoString % 256;
        $nHex16 = (int) ($nTamanhoString / 256);

        $nHex1 = dechex($nHex1);
        if(strlen($nHex1) === 1)
            $nHex1 = "0".$nHex1;

        $nHex16 = dechex($nHex16);
        if(strlen($nHex16) === 1)
            $nHex16 = "0".$nHex16;

        $sResultado = $nHex1." ".$nHex16;

        return strtoupper($sResultado);
    }

    function string2Hex($sString) {
        $sHex = "";
        $vString = str_split($sString);
        foreach($vString as $sCharactere)
            $sHex .= " ".dechex(ord($sCharactere)); // Transforms that character to ASCII and then converts it to hexadecimal

        return strtoupper($sHex);
    }

    function getCheckSum($sString) {
        $nTamanhoString = strlen($sString);

        $sXor = "";
        $vString = str_split($sString);
        foreach($vString as $sCharactere)
            $sXor ^= ord($sCharactere);

        $sXor ^= $nTamanhoString % 256;
        $sXor ^= $nTamanhoString / 256;

        $nHex1 = $sXor % 16;
        $nHex16 = (int) ($sXor / 16);

        $sResultado = " ".dechex($nHex16) . dechex($nHex1);

        return strtoupper($sResultado);
    }

    function hex2str($hex){
        $str='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $str .= chr(hexdec(substr($hex,$i,2)));
        }
        return $str;
    }

    public function flushBuffer()
    {
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $retval = "";
        if( !empty($socket) )
        {
            socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec'=>1,'usec'=>0));
            $result = @socket_connect($socket, $this->_ip, $this->_port);
            if( !empty($result) )
            {
                do
                {
                    $response = @socket_read($socket, self::MESSAGE_MAX_LEN);
                    $retval .= $response;
                }
                while(!empty($response));
            }
        }
        @socket_close($socket);
        return $retval;
    }

    function __destruct()
    {
        $this->socket = null;
        @socket_close($this->socket);
    }

    public function parseError($errorNumber)
    {
        $arrayError = array(
                    0   => "Não há erro",
                    1   => "Não há dados",
                    10	=> "Comando desconhecido",
                    11	=> "Tamanho do pacote é inválido",
                    12	=> "Parâmetros informados são inválidos",
                    13	=> "Erro de checksum",
                    14	=> "Tamanho dos parâmetros são inválidos",
                    15	=> "Número da mensagem é inválido",
                    16	=> "Start Byte é inválido",
                    17	=> "Erro para receber pacote",
                    20	=> "Não há empregador cadastrado",
                    21	=> "Não há usuários cadastrados",
                    22	=> "Usuário não cadastrado",
                    23	=> "Usuário já cadastrado",
                    24	=> "Limite de cadastro de usuários atingido",
                    25	=> "Equipamento não possui biometria",
                    26	=> "Index biométrico não encontrado",
                    27	=> "Limite de cadastro de digitais atingido",
                    28	=> "Equipamento não possui eventos",
                    29	=> "Erro na manipulação de biometrias",
                    30	=> "Documento do empregador é inválido",
                    31	=> "Tipo do documento do empregador é inválido",
                    32	=> "Ip é inválido",
                    33	=> "Tipo de operação do usuário é inválida",
                    34	=> "Identificador do empregado é inválido",
                    35	=> "Documento do empregador é inválido",
                    36	=> "Referencia do empregado é inválida",
                    37	=> "Referencia de cartão de usuario é inválida",
                    43	=> "Erro ao gravar dados",
                    44	=> "Erro ao ler dados",
                    50	=> "Erro desconhecido",
                    61	=> "Matrícula já existe",
                    62	=> "Identificador já existe",
                    63	=> "Opção inválida",
                    64	=> "Matrícula não existe",
                    65	=> "Identificador não existe",
                    66	=> "Cartão necessário mas não informado",
                    180	=> "Horário contido no usuário não existe",
                    181	=> "Período contido no horário não existe",
                    182	=> "Escala contida no usuário não existe",
                    183	=> "Faixa de dias da semana não informada ou inválida (acionamento e períodos)",
                    184	=> "Hora não informada ou inválida (acionamento e períodos)",
                    185	=> "Período não informado ou inválido (horários)",
                    186	=> "Horário não informado ou inválido (cartões)",
                    187	=> "Indice não informado ou inválido (horários, periodos e acionamentos)",
                    188	=> "Data não informada ou inválida (feriados)",
                    189	=> "Mensagem não informada (funções)",
                    190	=> "Erro na memoria (acionamento)",
                    191	=> "Mensagem não informada (funções)",
                    192	=> "Informação de tipo de acesso invalida",
                    193	=> "Informação de tipo de cartão invalida",
                    240	=> "Registro não foi encontrado (Grupos de acesso, período, horários, acionamentos)",
                    241	=> "Registro já existe (Grupos de acesso, período, horários, acionamentos)",
                    242	=> "Registro não existe (Grupos de acesso, período, horários, acionamentos)",
                    243	=> "Limite atingido (Grupos de acesso, período, horários, acionamentos)",
                    244	=> "Erro no tipo de operação (Grupos de acesso, período, horários, acionamentos)"
                    );

        return $arrayError[$errorNumber];
    }

}
    