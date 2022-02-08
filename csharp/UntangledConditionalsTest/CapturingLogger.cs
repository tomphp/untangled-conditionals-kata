using System;
using System.Collections.Generic;
using UntangledConditionals;

namespace TestProject1
{
    internal class CapturingLogger : Logger
    {
        public List<string> Lines { get; } = new List<string>();

        public void info(string message)
        {
            Lines.Add("INFO: " + message);
        }

        public void error(string message)
        {
            Lines.Add("ERROR: " + message);
        }
    }
}