module finalproject(	
	input clk, 
	input ps2c, 
	input ps2d,
	output logic [3:0] grounds,
	output logic [6:0] display, 
	input pushbutton,
	output logic       hsync,
   output logic       vsync,
   output logic [2:0] rgb);
	
	
logic [15:0] data_all;
logic [3:0] keyout;
logic ack;

//memory map is defined here

localparam BEGINMEM=12'h000,
				ENDMEM=12'h0df;
	
localparam	KEYBOARD_CHK = 12'h0fc,
				KEYBOARD_DAT = 12'h0fd,
				SEG_H= 12'h0fe,
				SEG_V= 12'h0ff;
// memory chip
logic [15:0] memory [0:512];


// cpu's input-output pins
logic [15:0] data_out;
logic [15:0] data_in;
logic [11:0] address;
logic memwt;

logic [15:0] data_out_key;
logic [25:0] clk1;

  logic [15:0] h_h;
  logic [15:0] v_v;
  

 vga_sync vga(.clk(clk),.hsync(hsync),.vsync(vsync),.rgb(rgb),.h_value(h_h),.v_value(v_v));
 sevensegment ss1(.clk(clk),.din(data_all),.grounds(grounds),.display(display));
 keyboard kb1(.clk(clk),.ps2d(ps2d),.ps2c(ps2c),.ack(ack),.dout(data_out_key));
 bird br1(.clk(clk),.data_in(data_in),.data_out(data_out),.address(address),.memwt(memwt));

always_ff @(posedge clk)
	begin
		data_all <= data_out_key;
	end


//multiplexer for cpu input
always_comb
begin
	
  if((BEGINMEM<=address) && (address<=ENDMEM))
	begin
	    data_in <= memory[address];
		 ack <= 0;
	end
  else if (address==KEYBOARD_CHK)
	begin
	 ack <= 0;
	 data_in <= data_out_key;
	end
   
  else if (address==KEYBOARD_DAT)
  begin
  	ack <= 1;
   data_in <= data_out_key;
  end
  else 
  begin
      data_in <= 16'h0000; //any number
		ack <= 0;
  end

end
	
	
//multiplexer for cpu output
always_ff @(posedge clk)
begin
	
	if (memwt)
	begin
		
		if((BEGINMEM<=address) && (address<=ENDMEM))
		begin
			memory[address]<=data_out;
		end
		else if(SEG_H == address)
		begin
			h_h <= data_out;
		end	
		else if(SEG_V == address)
		begin
				v_v <= data_out;
		end
		
	end
end


initial
	begin
		$readmemh("ram.dat", memory);
		data_out_key=16'h0000;
		data_all = 16'h0000;
		ack<=0;
		//memory[SEG_H] <=10'd160;
		//memory[SEG_V] <=10'd240;
	
	
	end
	
endmodule