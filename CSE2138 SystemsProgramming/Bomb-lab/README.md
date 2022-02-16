# Bomb-lab: Defusing a Binary Bomb 
This is a Reverse Engineering exercise

A binary bomb is a program that consists of a sequence of phases.

Each phase expects you to type a particular string on stdin.

If you type the correct string, then the phase is defused and the bomb proceeds to the next phase. Otherwise, the bomb explodes by printing "BOOM!!!" and then terminating. The bomb is defused when every phase has been defused.

Each bomb is a Linux binary executable file that has been compiled from a C program.

using objdump Linux command you can get the Assembly code of the Bomb.
