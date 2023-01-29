
import re
import sys




opcodes = {
    "ldi": 0x1,
    "ld": 0x2,
    "st": 0x3,
    "jz": 0x4,
    "jmp": 0x5,
    "alu": 0x7,
    "push": 0x8,
    "pop": 0x9,
    "call": 0xa,
    "ret": 0xb,
    "sti": 0xc,
    "cli": 0xd,
    "iret": 0xe
}
alucodes = {
    "add": 0x0,
    "sub": 0x1,
    "and": 0x2,
    "or": 0x3,
    "xor": 0x4,
    "not": 0x5,
    "mov": 0x6,
    "inc": 0x7,
    "dec": 0x8
}
reptile_8_alucodes = {
    "not": 0x0,
    "mov": 0x1,
    "inc": 0x2,
    "dec": 0x3
}

# A dictionary for jump labels.
# Key is a string of the name of the label. And value is an array with the size of 2
# First element contains the address of the label
# Second element contains an array with the line numbers where labels called from.
jumptable = {}

datatable = {}

machine_code = []

register_bit_size = 3
address_bit_size = 12


def get_inputfile(inputfile):
    return open(inputfile).read()


def get_elements(text):
    elements = []
    lines = re.split("\n+", text)
    for line in lines:
        before_comment = re.split("//", line)[0]
        if len(before_comment) == 0:
            continue
        elements.append(re.split("\s+|\t+", before_comment))
    return elements


def argument_handler(args):
    isLogisim = False
    inputfile = ""
    outputfile = ""
    try:
        for i in range(1, len(args)):
            if args[i] == "-i" or args[i] == "--input":
                i += 1
                inputfile = args[i]
            elif args[i] == "-o" or args[i] == "--output":
                i += 1
                outputfile = args[i]
            elif args[i] == "-h" or args[i] == "--help":
                show_help_message()
            elif args[i] == "-l" or args[i] == "--logisim":
                isLogisim = True
        return inputfile, outputfile, isLogisim
    except:
        print("Program ran via wrong input parameters check --help for detailed info")
        show_help_message()


def get_instruction_code(instruction, dest, src1, src2,idx):
    code = []
    binary = 0x0000
    if instruction is None:
        return
    if instruction in alucodes.keys():
        if (instruction == "add") or (instruction == "sub") or (instruction == "or") or (instruction == "and") or (
                instruction == "xor"):
            binary = (opcodes["alu"] << 12) + (alucodes[instruction] << 9) + (int(src1) << 6)\
                     + (int(src2) << 3) + (int(dest))
        elif instruction == "mov" or instruction == "not":
            binary = (opcodes["alu"] << 12) + (0x7 << 9) + (reptile_8_alucodes[instruction] << 6)\
                     + (int(src1) << 3) + (int(dest))
        elif instruction == "inc" or instruction == "dec":
            binary = (opcodes["alu"] << 12) + (0x7 << 9) + (reptile_8_alucodes[instruction] << 6)\
                     + (int(dest) << 3) + (int(dest))
    elif instruction == "ldi":
        binary = (opcodes[instruction] << 12) + (int(dest) & 0x7)
        code.append(binary)
        if str(src1).isnumeric():
            binary = (int(src1) & 0xffff)
        elif str(src1)[:2] == "0x":
            binary = int(src1[2:], 16)
        elif str(src1) in jumptable.keys():
            binary = int(jumptable[src1][0])
        else:
            binary = 0x0000
            add_data_request(src1, (idx+1))
    elif instruction == "ld":
        binary = (opcodes[instruction] << 12) + ((int(dest) << 3)) + (int(src1))
    elif instruction == "st":
        binary = (opcodes[instruction] << 12) + ((int(src1) << 6)) + (int(dest) << 3)
    elif (instruction == "jmp") or (instruction == "jz") or (instruction == "call"):
  
            binary = opcodes[instruction] << 12
            #code.append(binary)
            print(f"binary{format(binary, '04x')}\n")
            print(dest)
            if str(dest).isnumeric():
                binary = (int(dest) & 0xfff)
            else:
                add_jump_request(dest, (idx+1))
    elif instruction == "push":
        binary = (opcodes[instruction] << 12) + (int(dest) << 6)
    elif instruction == "pop":
        binary = (opcodes[instruction] << 12) + (int(dest))
    elif instruction == "ret":
        binary = (opcodes[instruction] << 12)
    elif instruction == "sti":
        binary = (opcodes[instruction] << 12)
    elif instruction == "cli":
        binary = (opcodes[instruction] << 12)
    elif instruction == "iret":
        binary = (opcodes[instruction] << 12) +0b110
    else:
        print(f"ERROR: Instruction '{instruction}' does not exist or not supported with this assembler")
   

    code.append(binary)
    print(f"binary son{format(binary, '04x')}\n")    
    return code


def get_parameters(elements):
    label = None
    instruction = None
    dest = None
    src1 = None
    src2 = None
    # Fill the variables due to length of the parameter in order to avoid array border infringement
    if len(elements) > 0 and elements[0] != "":
        label = elements[0]
    if len(elements) > 1:
        instruction = elements[1]
    if len(elements) > 2:
        dest = elements[2]
    if len(elements) > 3:
        src1 = elements[3]
    if len(elements) > 4:
        src2 = elements[4]
    return label, instruction, dest, src1, src2


# Adding the label to the table if not added
# If it is added check it is created by the add_jump_request function else throw an error.
def add_to_jumptable(name, current_line):
    if name not in jumptable.keys():
        jumptable[name] = [current_line, []]
    else:
        if jumptable[name][0] == -1:
            jumptable[name][0] = current_line
        else:
            print(f"Error: 2 jumppoints is declared with the same name {name}")


# Adding the line to the tables request array.
# If the label is not created, creates one with the address of -1 and adds the address of the request
def add_jump_request(name, assembly_line):
    if name not in jumptable.keys():
        jumptable[name] = [-1, []]
    jumptable[name][1].append(assembly_line)


def get_jump_offset(name, current_line):
    if name in jumptable.keys():
        result = jumptable[name][0] - current_line
        if result < 0:
            result += (pow(2, address_bit_size))
        return result & (pow(2, address_bit_size) - 1)
    else:
        print(f"in line {current_line}: Address {name} is not found on declared addresses.")


# implementation of .space is ignored in this phase of development
def add_to_datatable(name, value):
    if str(value)[:2] == "0x":
        value = int(value[2:], 16)
    if name not in datatable.keys():
        datatable[name] = [[value, -1], []]
    else:
        if datatable[name][0][0] == -1:
            datatable[name][0][0] = value
        else:
            raise Exception(f"Error: to variables created with the same name '{name}'")


def add_data_request(name, assembly_line):
    if name not in datatable.keys():
        datatable[name] = [[-1, -1], []]
    datatable[name][1].append(assembly_line)


def get_data_offset(name):
    if name in datatable.keys():
        print(name)
        result = datatable[name][0][1]
        if result < 0:
            result += (pow(2, address_bit_size) - 1)
        return result & (pow(2, address_bit_size) - 1)
    return 0


def show_help_message():
    print("help message")
    print("-i or --input for input")
    print("-o or --output for output")
    print("-l for logisim output ('v2.0 raw' in the header)")
    print("example: python asssembler.py -i machine_code.txt -o RAM -l")
    exit()


def write_output(outputfile, code, is_logisim):
    ram = open(outputfile, "w")
    if is_logisim:
        ram.write("v2.0 raw\n")
    for i in code:
        ram.write(f"{format(i, '04x')}\n")


def get_data_section(instructions):
    i = 0
    for instruction in instructions:
        if instruction[0] == ".code":
            return instructions[1:i]
        else:
            i += 1
    print(".DATA SECTION IS NOT FOUND IN THE INPUT")
    pass


def get_code_section(instructions):
    i = 0
    for instruction in instructions:
        if instruction[0] == ".code":
            return instructions[i+1:]
        else:
            i += 1
    raise Exception(".CODE SECTION IS NOT FOUND IN THE INPUT")


if __name__ == '__main__':
    in_file, out_file, is_logisim = argument_handler(sys.argv)
    print(f"Arguments are: input:{in_file}   output:{out_file}  is for logisim:{is_logisim}")
    code_text = get_inputfile(in_file)
    elements = get_elements(code_text)
    print(elements)
    machine_code = []

    for element in get_data_section(elements):
        add_to_datatable(element[1][:-1], element[2])

    for element in get_code_section(elements):
        print(element)
        label, instruction, dest, src1, src2 = get_parameters(element)
        if label is not None:
            print(f"adding {label} to the jumptable")
            add_to_jumptable(label, len(machine_code))

        instruction_code = get_instruction_code(instruction, dest, src1, src2, len(machine_code))
        if instruction_code is None:
            continue
        for code in instruction_code:
            machine_code.append(code)

    for label in jumptable.keys():
        for i in jumptable[label][1]:
            machine_code[i-1] += get_jump_offset(label, i)

    for data in datatable.keys():
        machine_code.append(int(datatable[data][0][0]))
        datatable[data][0][1] += len(machine_code)

    for data in datatable.keys():
        for i in datatable[data][1]:
            machine_code[i] += get_data_offset(data)

    write_output(out_file, machine_code, is_logisim)