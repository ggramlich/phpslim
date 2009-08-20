package slim.testModule;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;

import fitnesse.slim.StatementExecutorInterface;
import fitnesse.slim.test.TestSlimInterface;
import fitnesse.slim.test.Zork;

public class TestSlimProxy implements TestSlimInterface {
  private String testSlim;
  private StatementExecutorInterface caller;

  public TestSlimProxy(String testSlim, StatementExecutorInterface caller) {
    this.testSlim = testSlim;
    this.caller = caller;
  }

  public boolean niladWasCalled() {
    return "true".equals(caller.call(testSlim, "niladWasCalled")); 
  }

  public String getStringArg() {
    return (String) caller.call(testSlim, "getValue");
  }

  public int getIntArg() {
    return Integer.parseInt((String) caller.call(testSlim, "getValue"));
  }

  public double getDoubleArg() {
    return Double.parseDouble((String) caller.call(testSlim, "getValue"));
  }

  public Date getDateArg() {
    try {
      return (new SimpleDateFormat("dd-MMM-yyyy", Locale.US)).parse((String) caller.call(testSlim, "getValue"));
    } catch (ParseException e) {
      return null;
    }
  }

  @SuppressWarnings("unchecked")
  public List<Object> getListArg() {
    return (List<Object>) caller.call(testSlim, "getListArg");
  }

  public Integer getIntegerObjectArg() {
    return Integer.parseInt((String) caller.call(testSlim, "getIntegerObjectArg"));
  }

  public double getDoubleObjectArg() {
    return Double.valueOf((String) caller.call(testSlim, "getDoubleObjectArg"));
  }
  
  public char getCharArg() {
    String result = (String) caller.call(testSlim, "getCharArg");
    if (1 != result.length()) {
      return '_';
    }
    return result.toCharArray()[0];
  }

  @Override
  public Zork getZork() {
    return null;
  }
  
}
